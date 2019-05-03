<?php

namespace Drupal\task_api\Plugin\task_api\Action;

use Drupal\Core\Plugin\PluginBase;
use Drupal\task_api\TaskActionInterface;
use Drupal\task_api\Entity\TaskInterface;

/**
 * @TaskAction(
 *   id = "expire",
 *   label = @Translation("Expire Task"),
 *   system_task = TRUE,
 * )
 */
class Expire extends PluginBase implements TaskActionInterface {

  /**
   * @return string
   *   A string description.
   */
  public function description() {
    return $this->t('Automatically expire an item');
  }

  /**
   * Since this is a default, just return what we have.
   */
  public static function doAction(TaskInterface $task, $data = []) {
    $task->set('status', 'closed');
    $task->set('close_date', time());
    $task->set('close_type', 'expired');
    $task->save();
  }
}