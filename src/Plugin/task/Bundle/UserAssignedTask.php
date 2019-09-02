<?php

namespace Drupal\task\Plugin\task\Bundle;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\task\Entity\Task;
use Drupal\task\Entity\TaskInterface;
use Drupal\task\TaskBundleInterface;

/**
 * @TaskBundle(
 *   id = "user_assigned_task",
 *   label = @Translation("User-Assigned Task"),
 *   bundle = "user_assigned_task"
 * )
 */
class UserAssignedTask extends PluginBase implements TaskBundleInterface {

  /**
   * @return string
   *   A string description.
   */
  public function description()
  {
    return $this->t('Actions for System Tasks');
  }

  /**
   * @param $task_data
   */
  public static function createTask($task_data) {
    $task = Task::create($task_data);
    $data = $task->get('task_data')->getValue();
    $task->save();
    if (isset($data[0]['actions']['create'])) {
      $action_data = $data[0]['actions']['create'];
      foreach ($action_data as $id => $data) {
        $plugin_manager = \Drupal::service('plugin.manager.task_action');
        $plugin_definitions = $plugin_manager->getDefinitions();
        if(isset($plugin_definitions[$id])) {
          $plugin_definitions[$id]['class']::doAction($task, $data);
        }
      }
    }
    return $task;
  }

  /**
   * @param $task_data
   */
  public static function expireTask($task) {
    $data = $task->get('task_data')->getValue();
    if (isset($data[0]['actions']['expire'])) {
      $action_data = $data[0]['actions']['expire'];
      foreach ($action_data as $id => $data) {
        $plugin_manager = \Drupal::service('plugin.manager.task_action');
        $plugin_definitions = $plugin_manager->getDefinitions();
        if(isset($plugin_definitions[$id])) {
          $plugin_definitions[$id]['class']::doAction($task, $data);
        }
      }
    }
    $task->set('status', 'closed');
    $task->set('close_date', time());
    $task->set('close_type', 'completed');
    $task->save();
    return $task;
  }

  /**
   * @param TaskInterface $task
   * @return array
   */
  public static function getTaskOptions(TaskInterface $task) {
    $url_complete = Url::fromRoute('task.mark_complete', ['task' => $task->id()]);
    $link_complete = Link::fromTextAndUrl('Mark Complete', $url_complete);
    $url_dismiss = Url::fromRoute('task.dismiss', ['task' => $task->id()]);
    $link_dismiss = Link::fromTextAndUrl('Dismiss', $url_dismiss);

    return ['#type' => 'markup', '#markup' => implode(', ', [$link_dismiss->toString()->getGeneratedLink(), $link_complete->toString()->getGeneratedLink()])];
  }

}
