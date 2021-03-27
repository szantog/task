<?php

namespace Drupal\task\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\task\Plugin\task\Action\MarkComplete;

/**
 * Provides a form for Task action confirmations.
 *
 * @ingroup task
 */
class TaskActionConfirmForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return;
  }

  /**
   * @inheritDoc
   */
  public function getQuestion() {
    switch ($this->getOperation()) {
      case 'mark-complete':
        return $this->t('Are you sure you want to mark %task task complete?', [
          '%task' => $this->entity->label()
        ]);
    }
  }

  /**
   * We don't need cancel url.
   */
  public function getCancelUrl() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    return [
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->getConfirmText(),
        '#submit' => [
          [$this, 'save'],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * The save() method is not used in EntityConfirmFormBase. This overrides the
   * default implementation that saves the entity.
   *
   * Confirmation forms should override submitForm() instead for their logic.
   */
  public function save(array $form, FormStateInterface $form_state) {
    switch ($this->getOperation()) {
      case 'mark-complete':
        MarkComplete::doAction($this->entity);
        $this->messenger()->addStatus($this->t('Task %task was marked as complete.', [
          '%task' => $this->entity->label()
        ]));
    }
  }

  /**
   * {@inheritdoc}
   *
   * The delete() method is not used in EntityConfirmFormBase. This overrides
   * the default implementation that redirects to the delete-form confirmation
   * form.
   *
   * Confirmation forms should override submitForm() instead for their logic.
   */
  public function delete(array $form, FormStateInterface $form_state) {

  }

  /**
   * Disable parent afterbuild because it messes up the entity in this case.
   */
  public function afterBuild(array $element, FormStateInterface $form_state) {
    return $element;
  }

}
