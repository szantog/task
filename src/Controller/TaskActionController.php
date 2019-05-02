<?php
/**
 * @file
 */

namespace Drupal\task_api\Controller;

use Drupal\task_api\TaskActionManager;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TaskActionController
 *
 * Provides the route and API controller for task_api.
 */
class TaskActionController extends ControllerBase
{

  protected $TaskActionManager; //The plugin manager.

  /**
   * Constructor.
   *
   * @param \Drupal\task_api\TaskActionManager $plugin_manager
   */

  public function __construct(TaskActionManager $plugin_manager) {
    $this->TaskActionManager = $plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Use the service container to instantiate a new instance of our controller.
    return new static($container->get('plugin.manager.task_api_action'));
  }

}
