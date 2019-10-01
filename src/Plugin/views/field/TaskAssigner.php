<?php

namespace Drupal\task\Plugin\views\field;

use Drupal\task\TaskUtilities;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\views_add_button\ViewsAddButtonUtilities;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Defines a views field plugin.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("task_assigner")
 */
class TaskAssigner extends FieldPluginBase {

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
    $options['system_label'] = ['default' => 'System'];
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
      $assigner = $task->getAssigner();
      if ($assigner) {
        $label = $this->options['link'] ? $assigner->toLink($assigner->label(), 'canonical')->toString()->getGeneratedLink() : $assigner->label();
        return ['#type' => 'markup', '#markup' => $label];
      }
    }
    return ['#type' => 'markup', '#markup' => t($this->options['system_label'])];
  }

}
