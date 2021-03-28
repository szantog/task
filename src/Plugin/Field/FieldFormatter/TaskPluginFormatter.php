<?php

namespace Drupal\task\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\task\TaskUtilities;

/**
 * Plugin implementation of the 'Task Plugin' formatter.
 *
 * @FieldFormatter(
 *   id = "task_task_plugin",
 *   label = @Translation("Task Plugin"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class TaskPluginFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    $statuses = TaskUtilities::getAllTaskStatuses();
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $statuses[$item->value],
      ];
    }

    return $element;
  }

}
