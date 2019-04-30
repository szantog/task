<?php

namespace Drupal\task_api\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TaskClosureReasonForm.
 */
class TaskClosureReasonForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $task_closure_reason = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $task_closure_reason->label(),
      '#description' => $this->t("Label for the Task Closure Reason."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $task_closure_reason->id(),
      '#machine_name' => [
        'exists' => '\Drupal\task_api\Entity\TaskClosureReason::load',
      ],
      '#disabled' => !$task_closure_reason->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $task_closure_reason = $this->entity;
    $status = $task_closure_reason->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Task Closure Reason.', [
          '%label' => $task_closure_reason->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Task Closure Reason.', [
          '%label' => $task_closure_reason->label(),
        ]));
    }
    $form_state->setRedirectUrl($task_closure_reason->toUrl('collection'));
  }

}
