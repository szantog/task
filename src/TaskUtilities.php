<?php

namespace Drupal\task_api;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\task_api\Entity\TaskStatus;

class TaskUtilities {

  public static function getAllTaskStatuses() {
    $entitites = \Drupal::entityTypeManager()->getStorage('task_status')->getQuery()->execute();
    // There is always a "closed" status.
    $statuses = ['closed' => 'Closed'];
    foreach ($entitites as $entity) {
      $ent = TaskStatus::load($entity);
      $statuses[$ent->id()] = $ent->label();
    }
    ksort($statuses);
    return $statuses;
  }

}
