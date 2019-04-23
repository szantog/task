<?php

namespace Drupal\task_api;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface ClassTaskStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Task revision IDs for a specific Task.
   *
   * @param \Drupal\task_api\Entity\ClassTaskInterface $entity
   *   The Task entity.
   *
   * @return int[]
   *   Task revision IDs (in ascending order).
   */
  public function revisionIds(ClassTaskInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Task author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Task revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\task_api\Entity\ClassTaskInterface $entity
   *   The Task entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(ClassTaskInterface $entity);

  /**
   * Unsets the language for all Task with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
