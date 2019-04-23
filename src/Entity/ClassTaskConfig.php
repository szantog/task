<?php

namespace Drupal\task_api\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Task Configuration entity.
 *
 * @ConfigEntityType(
 *   id = "task_config",
 *   label = @Translation("Task Configuration"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\task_api\ClassTaskConfigListBuilder",
 *     "form" = {
 *       "add" = "Drupal\task_api\Form\ClassTaskConfigForm",
 *       "edit" = "Drupal\task_api\Form\ClassTaskConfigForm",
 *       "delete" = "Drupal\task_api\Form\ClassTaskConfigDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\task_api\ClassTaskConfigHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "task_config",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/task_config/{task_config}",
 *     "add-form" = "/admin/structure/task_config/add",
 *     "edit-form" = "/admin/structure/task_config/{task_config}/edit",
 *     "delete-form" = "/admin/structure/task_config/{task_config}/delete",
 *     "collection" = "/admin/structure/task_config"
 *   }
 * )
 */
class ClassTaskConfig extends ConfigEntityBase implements ClassTaskConfigInterface {

  /**
   * The Task Configuration ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Task Configuration label.
   *
   * @var string
   */
  protected $label;

}
