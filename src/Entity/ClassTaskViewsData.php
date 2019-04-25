<?php

namespace Drupal\task_api\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Task entities.
 */
class ClassTaskViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}