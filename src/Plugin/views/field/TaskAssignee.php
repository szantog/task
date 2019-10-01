<?php

namespace Drupal\task\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a views field plugin.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("task_assignee")
 */
class TaskAssignee extends FieldPluginBase {

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
    $options['link'] = ['default' => FALSE];
    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['link'] = [
      '#type' => 'checkbox',
      '#title' => t('Link to the entity'),
      '#default_value' => isset($this->options['link']) ? $this->options['link'] : FALSE,
    ];
    $form['system_label'] = [
      '#type' => 'textfield',
      '#title' => t('System Assignment Label'),
      '#default_value' => isset($this->options['system_label']) ? $this->options['system_label'] : FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $task = $this->getEntity($values);
    if ($task && $task->getEntityTypeId() === 'task') {
      /* @var $task \Drupal\task\Entity\Task */
      $assignee = $task->getAssignee();
      if ($assignee) {
        $label = $this->options['link'] ? $assignee->toLink($assignee->label(), 'canonical')->toString()->getGeneratedLink() : $assignee->label();
        return ['#type' => 'markup', '#markup' => $label];
      }
    }
    return ['#type' => 'markup', '#markup' => t($this->options['system_label'])];
  }

}
