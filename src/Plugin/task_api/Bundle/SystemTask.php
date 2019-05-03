<?php

namespace Drupal\task_api\Plugin\task_api\Bundle;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\task_api\Entity\Task;
use Drupal\task_api\Entity\TaskInterface;
use Drupal\task_api\TaskBundleInterface;

/**
 * @TaskBundle(
 *   id = "system_task",
 *   label = @Translation("System Task"),
 *   bundle = "system_task"
 * )
 */
class SystemTask extends PluginBase implements TaskBundleInterface {

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
        $plugin_manager = \Drupal::service('plugin.manager.task_api_action');
        $plugin_definitions = $plugin_manager->getDefinitions();
        if(isset($plugin_definitions[$id])) {
          $plugin_definitions[$id]['class']::doAction($task, $data);
        }
      }
    }
    return $task;
  }

  public static function getTaskOptions(TaskInterface $task) {
    $url_complete = Url::fromRoute('task_api.mark_complete', ['task' => $task->id()]);
    $link_complete = Link::fromTextAndUrl('Mark Complete', $url_complete);
    $url_dismiss = Url::fromRoute('task_api.dismiss', ['task' => $task->id()]);
    $link_dismiss = Link::fromTextAndUrl('Dismiss', $url_dismiss);

    return ['#type' => 'markup', '#markup' => implode(', ', [$link_dismiss->toString(), $link_complete->toString()])];
  }

}
