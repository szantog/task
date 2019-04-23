<?php

namespace Drupal\task_api;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\task_api\Entity\ClassTaskInterface;

/**
 * Defines the storage handler class for Task entities.
 *
 * This extends the base storage class, adding required special handling for
 * Task entities.
 *
 * @ingroup task_api
 */
class ClassTaskStorage extends SqlContentEntityStorage implements ClassTaskStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(ClassTaskInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {task_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {task_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(ClassTaskInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {task_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('task_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
