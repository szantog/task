<?php

namespace Drupal\task;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Task entities.
 *
 * @ingroup task
 */
class TaskListBuilder extends EntityListBuilder {


  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Task ID');
    $header['name'] = $this->t('Name');
    $header['status'] = $this->t('Status');
    $header['expire'] = $this->t('Expiration Date');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\task\Entity\Task */
    $row['id'] = $entity->id();
    $row['name'] = $entity->label();
    $row['status'] = $entity->getStatus();
    $expire = $entity->get('expire_date')->value;
    $row['expire'] = date('Y-m-d H:i:s', $expire);
    return $row + parent::buildRow($entity);
  }

}
