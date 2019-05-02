<?php

namespace Drupal\task_api\Plugin\task_api\Action;

use Drupal\Core\Plugin\PluginBase;
use Drupal\task_api\TaskActionInterface;
use Drupal\task_api\Entity\TaskInterface;

/**
 * @TaskAction(
 *   id = "send_mail",
 *   label = @Translation("Send Mail"),
 *   system_task = TRUE,
 * )
 */
class SendMail extends PluginBase implements TaskActionInterface {

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
    // TODO: Write mail function.
    $module = isset($data['module']) ? $data['module'] : 'task_api';
    $key = isset($data['key']) ? $data['key'] : 'task_api_mail';
    $to = isset($data['to']) ? $data['to'] : '';
    $langcode = isset($data['langcode']) ? $data['langcode'] : '';
    $params = isset($data['params']) ? $data['params'] : [];
    $reply = isset($data['reply']) ? $data['reply'] : NULL;
    $send = isset($data['send']) ? $data['send'] : TRUE;
    $mailManager = \Drupal::service('plugin.manager.mail');
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $reply, $send);
  }
}