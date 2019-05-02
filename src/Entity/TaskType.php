<?php

namespace Drupal\task_api\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Task type entity.
 *
 * @ConfigEntityType(
 *   id = "task_type",
 *   label = @Translation("Task type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\task_api\TaskTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\task_api\Form\TaskTypeForm",
 *       "edit" = "Drupal\task_api\Form\TaskTypeForm",
 *       "delete" = "Drupal\task_api\Form\TaskTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\task_api\TaskTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "task_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "task",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/task_api/task_type/{task_type}",
 *     "add-form" = "/admin/structure/task_api/task_type/add",
 *     "edit-form" = "/admin/structure/task_api/task_type/{task_type}/edit",
 *     "delete-form" = "/admin/structure/task_api/task_type/{task_type}/delete",
 *     "collection" = "/admin/structure/task_api/task_type"
 *   }
 * )
 */
class TaskType extends ConfigEntityBundleBase implements TaskTypeInterface {

  /**
   * The Task type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Task type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Task type description.
   *
   * @var string
   */
  protected $description;

  /**
   * The list of allowed statuses.
   *
   * @var array
   */
  protected $allowed_statuses;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return !empty($this->description) ? $this->description : '';
  }

  /**
   * {@inheritdoc}
   */
  public function getAllowedStatuses() {
    $statuses = !empty($this->allowed_statuses) ? $this->allowed_statuses : [];
    $statuses['closed'] = 'closed';
    return $statuses;
  }

  /**
   * {@inheritdoc}
   */
  public function setAllowedStatuses($statuses) {
    $this->allowed_statuses = $statuses;
  }

}
