<?php

namespace Drupal\task_api;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Task entity.
 *
 * @see \Drupal\task_api\Entity\ClassTask.
 */
class ClassTaskAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\task_api\Entity\ClassTaskInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished task entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published task entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit task entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete task entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add task entities');
  }

}
