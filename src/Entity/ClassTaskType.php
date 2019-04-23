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
 *     "list_builder" = "Drupal\task_api\ClassTaskTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\task_api\Form\ClassTaskTypeForm",
 *       "edit" = "Drupal\task_api\Form\ClassTaskTypeForm",
 *       "delete" = "Drupal\task_api\Form\ClassTaskTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\task_api\ClassTaskTypeHtmlRouteProvider",
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
 *     "canonical" = "/admin/structure/task_type/{task_type}",
 *     "add-form" = "/admin/structure/task_type/add",
 *     "edit-form" = "/admin/structure/task_type/{task_type}/edit",
 *     "delete-form" = "/admin/structure/task_type/{task_type}/delete",
 *     "collection" = "/admin/structure/task_type"
 *   }
 * )
 */
class ClassTaskType extends ConfigEntityBundleBase implements ClassTaskTypeInterface {

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

}
