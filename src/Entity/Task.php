<?php

namespace Drupal\task\Entity;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Defines the Task entity.
 *
 * @ingroup task
 *
 * @ContentEntityType(
 *   id = "task",
 *   label = @Translation("Task"),
 *   bundle_label = @Translation("Task type"),
 *   handlers = {
 *     "storage" = "Drupal\task\TaskStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\task\TaskListBuilder",
 *     "views_data" = "Drupal\task\Entity\TaskViewsData",
 *     "translation" = "Drupal\task\TaskTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\task\Form\TaskForm",
 *       "add" = "Drupal\task\Form\TaskForm",
 *       "edit" = "Drupal\task\Form\TaskForm",
 *       "delete" = "Drupal\task\Form\TaskDeleteForm",
 *       "mark-complete" = "Drupal\task\Form\TaskActionConfirmForm",
 *     },
 *     "access" = "Drupal\task\TaskAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\task\TaskHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "task",
 *   data_table = "task_field_data",
 *   revision_table = "task_revision",
 *   revision_data_table = "task_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer task entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/task/{task}",
 *     "add-page" = "/task/add",
 *     "add-form" = "/task/add/{task_type}",
 *     "edit-form" = "/task/{task}/edit",
 *     "delete-form" = "/task/{task}/delete",
 *     "version-history" = "/task/{task}/revisions",
 *     "revision" = "/task/{task}/revisions/{task_revision}/view",
 *     "revision_revert" = "/task/{task}/revisions/{task_revision}/revert",
 *     "revision_delete" = "/task/{task}/revisions/{task_revision}/delete",
 *     "translation_revert" = "/task/{task}/revisions/{task_revision}/revert/{langcode}",
 *     "collection" = "/admin/content/task",
 *     "mark-complete" = "/task/{task}/mark_complete",
 *     "dismiss" = "/task/{task}/dismiss",
 *     "manual_expire" = "/task/{task}/manual_expire"
 *   },
 *   bundle_entity_type = "task_type",
 *   field_ui_base_route = "entity.task_type.edit_form"
 * )
 */
class Task extends RevisionableContentEntityBase implements TaskInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the task owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * Not used, but part of the RevisionableContentEntityBase class.
   *
   * @return string
   */
  public function getName() {
    // This function is not used.
    return '';
  }

  /**
   * @param string $field
   *
   * @return bool|\Drupal\Core\Entity\EntityInterface|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAssignmentEntity($field = 'assigned_to') {
    if (in_array($field, ['assigned_to', 'assigned_by'])) {
      $entity_id = $this->get($field)->getValue();
      $entity_id = isset($entity_id[0]['value']) ? $entity_id[0]['value'] : 0;
      //      $entity_type = $this->get($field . '_type')->getValue();
      //      $entity_type = isset($entity_type[0]['value']) ? $entity_type[0]['value'] : FALSE;
      $entity_type = 'user';
      if ($entity_id && $entity_type) {
        $entity = \Drupal::entityTypeManager()
          ->getStorage($entity_type)
          ->load($entity_id);
        return $entity;
      }
    }
    // Either we had the wrong field value, or incomplete data.
    return FALSE;
  }

  /**
   * @return bool|\Drupal\Core\Entity\EntityInterface|null
   */
  public function getAssignee() {
    return $this->getAssignmentEntity('assigned_to');
  }

  /**
   * Gets assignees.
   */
  public function getAssignees() {
    if (!empty($this->get('assigned_to'))) {
      return $this->get('assigned_to')->referencedEntities();
    }
    return [];
  }

  /**
   * @return bool|\Drupal\Core\Entity\EntityInterface|null
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAssigner() {
    return $this->getAssignmentEntity('assigned_by');
  }

  /**
   * Not used, but part of the RevisionableContentEntityBase class.
   *
   * @param string $name
   *
   * @return $this|TaskInterface
   */
  public function setName($name) {
    // This function is not used.
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * Not used, but part of the RevisionableContentEntityBase class.
   *
   * @return bool
   */
  public function isPublished() {
    // We do not use this method.
    return TRUE;
  }

  /**
   * Not used, but part of the RevisionableContentEntityBase class.
   *
   * @param bool $published
   *
   * @return TaskInterface|void
   */
  public function setPublished($published = FALSE) {
    // We do not use this method.
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Task entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Task entity.'))
      ->setRevisionable(TRUE)
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    /*
     * Assignment Fields.
     */
    $fields['parent_task'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Parent Task'))
      ->setDescription(t('The parent task, if this a a sub-task.'))
      ->setSetting('target_type', 'task')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'parent',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);
    $fields['assigned_by_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Assigner Entity Type'))
      ->setDescription(t('Entity type of the assignee. Typically a user, or blank for system tasks.'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDisplayConfigurable('view', FALSE);
    $fields['assigned_to_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Assignee Entity Type'))
      ->setDescription(t('Entity type of the assignee. Typically a user, or blank for system tasks.'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDisplayConfigurable('view', FALSE);
    $fields['assigned_by'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Assigned by'))
      ->setDescription(t('The User who assigned the task, or blank for system tasks.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['assigned_to'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Assigned to'))
      ->setDescription(t('The User to whom the task is assigned.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    /*
     * Date fields.
     */
    $fields['due_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Due Date'))
      ->setDescription(t('If assigned to an entity, this is the "due date" that will display. This is not used by system tasks.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'parent',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);
    $fields['expire_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Expire Date'))
      ->setDescription(t('This is an "expiration date" that will force-close the task. For system tasks, this is also the date at which the action should be executed.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'parent',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);
    $fields['close_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Close Date'))
      ->setDescription(t('The date the task was closed.'))
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'parent',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    /*
     * Additional fields
     */
    $fields['status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Task Status'))
      ->setDescription(t('The current status of a task.'))
      ->setSettings([
        'default_value' => 'active',
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'parent',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);
    $fields['close_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Close Type'))
      ->setDescription(t('A string representing the reason the task was closed.'))
      ->setSettings([
        'default_value' => 'active',
        'max_length' => 255,
        'text_processing' => 0,
      ]);
    $fields['task_data'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Task Data'))
      ->setDescription(t('This is a freeform serialized array, to be used by custom plugins.'))
      ->setSettings([
        'default_value' => 'active',
        'max_length' => 255,
        'text_processing' => 0,
      ]);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function toUrl($rel = 'canonical', array $options = []) {
    if (in_array($rel, ['mark-complete', 'dismiss', 'manual_expore'])) {
      $new_options = [
        'attributes' => [
          'id' => Html::getUniqueId($rel),
          'class' => [
            'use-ajax',
          ],
          'data-dialog-type' => 'modal',
          'data-dialog-options' => Json::encode([
            'width' => 700,
          ]),
        ],
        'query' => [
          'destination' => \Drupal::requestStack()->getCurrentRequest()->getRequestUri(),
        ],
      ];
      $options = array_merge($options, $new_options);
    }

    return parent::toUrl($rel, $options);
  }
}
