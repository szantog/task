<?php

namespace Drupal\task\Plugin\views\field;

use Drupal\task\TaskUtilities;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\Core\Form\FormStateInterface;


/**
 * Defines a views field plugin.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("task_options")
 */
class TaskOptions extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options.
   *
   * @return array
   *   Array of available options for views_add_button form.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $task = $this->getEntity($values);
    if ($task && $task->getEntityTypeId() === 'task') {
      /* @var $task \Drupal\task\Entity\Task */
      return TaskUtilities::getTaskOptions($task);
    }
    else {
      return ['#type' => 'markup', '#markup' => ''];
    }
  }

}
