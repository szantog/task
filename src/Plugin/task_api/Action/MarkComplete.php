<?php

namespace Drupal\task_api\Plugin\task_api\Action;

use Drupal\Core\Plugin\PluginBase;
use Drupal\task_api\TaskActionInterface;
use Drupal\task_api\Entity\TaskInterface;

/**
 * @TaskAction(
 *   id = "mark_complete",
 *   label = @Translation("MarkComplete"),
 *   system_task = FALSE,
 * )
 */
class MarkComplete extends PluginBase implements TaskActionInterface {

  /**
   * @return string
   *   A string description.
   */
  public function description()
  {
    return $this->t('This is a description of the default plugin.');
  }

  /**
   * Since this is a default, just return what we have.
   */
  public static function doAction(TaskInterface $task, $data = []) {
    $task->set('status', 'closed');
    $task->set('close_date', time());
    $task->set('close_type', 'completed');
    $task->save();
  }

}