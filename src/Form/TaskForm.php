<?php

namespace Drupal\task\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Date;
use Drupal\task\Entity\Task;
use Drupal\task\TaskUtilities;
use Drupal\user\Entity\User;

/**
 * Form controller for Task edit forms.
 *
 * @ingroup task
 */
class TaskForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\task\Entity\Task */
    $form = parent::buildForm($form, $form_state);

    // Variable Logic to initialize before form creation
    $entity       = $this->entity;
    $values       = $entity->toArray();
    // Check if the values are already set in the DB, else NULL.
    $task_type    = isset($values['type'][0]['target_id']) ? $values['type'][0]['target_id'] : NULL;
    $due_date     = isset($values['due_date'][0]['value']) ? $values['due_date'][0]['value'] : NULL;
    $exp_date     = isset($values['expire_date'][0]['value']) ? $values['expire_date'][0]['value'] : NULL;
    $parent       = isset($values['parent_task'][0]['target_id']) ? $entity::load($values['parent_task'][0]['target_id']) : NULL;
    // Format dates
    $date_stamp   = $due_date ? \Drupal::service('date.formatter')->format($due_date, 'custom', 'Y-m-d') : NULL;
    $expire_stamp = $exp_date ? \Drupal::service('date.formatter')->format($exp_date, 'custom', 'Y-m-d') : NULL;

    $form['parent_task'] = [
      '#type'          => 'entity_autocomplete',
      '#title'         => t('Parent Task:'),
      '#target_type'   => 'task',
      '#default_value' => $parent ? $parent : NULL,
      '#weight'        => 10,
    ];

    if (!$this->entity->isNew()) {
      $form['new_revision'] = [
        '#type'          => 'checkbox',
        '#title'         => $this->t('Create new revision'),
        '#default_value' => FALSE,
        '#weight'        => 11,
      ];
    }

    $form['status'] = [
      '#type'          => 'radios',
      '#title'         => $this->t('Status'),
      '#default_value' => $entity->getStatus() ? $entity->getStatus() : 'active',
      '#options'       => TaskUtilities::getAllTaskStatuses(),
      '#required'      => TRUE,
      '#weight'        => 12,
    ];

    $form['due_date'] = [
      '#type'          => 'date',
      '#title'         => 'Due By:',
      '#description'   => 'The time when this task should be completed, but will not automatically close.',
      '#default_value' => $date_stamp,
      '#weight'        => 15,
    ];

    $form['expire_date'] = [
      '#type'          => 'date',
      '#title'         => 'Expire By:',
      '#description'   => 'The date that will automatically force-close this task.',
      '#default_value' => $expire_stamp,
      '#weight'        => 16,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $entity->set('status', $form_state->getValue('status'));

    //Get and Set Due_Date from Format 'Y-m-d' to Unix Format in DB.
    $due_date = $form_state->getValue('due_date');
    $unix_time = strtotime($due_date);
    $entity->set('due_date', $unix_time);

    //Get and Set Exp_Date from Format 'Y-m-d' to Unix Format in DB.
    $exp_date = $form_state->getValue('expire_date');
    $unix_time = strtotime($exp_date);
    $entity->set('expire_date', $unix_time);

    // Save as a new revision if requested to do so.
    if (!$form_state->isValueEmpty('new_revision') && $form_state->getValue('new_revision') != FALSE) {
      $entity->setNewRevision();

      // If a new revision is created, save the current user as revision author.
      $entity->setRevisionCreationTime(REQUEST_TIME);
      $entity->setRevisionUserId(\Drupal::currentUser()->id());
    }
    else {
      $entity->setNewRevision(FALSE);
    }

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        \Drupal::messenger()->addMessage($this->t('Created the %label Task.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        \Drupal::messenger()->addMessage($this->t('Saved the %label Task.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.task.canonical', ['task' => $entity->id()]);
  }

}
