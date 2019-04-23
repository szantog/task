<?php

namespace Drupal\task_api\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ClassTaskConfigForm.
 */
class ClassTaskConfigForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $task_config = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $task_config->label(),
      '#description' => $this->t("Label for the Task Configuration."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $task_config->id(),
      '#machine_name' => [
        'exists' => '\Drupal\task_api\Entity\ClassTaskConfig::load',
      ],
      '#disabled' => !$task_config->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $task_config = $this->entity;
    $status = $task_config->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Task Configuration.', [
          '%label' => $task_config->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Task Configuration.', [
          '%label' => $task_config->label(),
        ]));
    }
    $form_state->setRedirectUrl($task_config->toUrl('collection'));
  }

}
