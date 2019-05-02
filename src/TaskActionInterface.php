<?php

/**
 * @file
 * Provides Drupal\task_api\TaskActionInterface;
 */

namespace Drupal\task_api;

/**
 * An interface for all TaskAction type plugins.
 */
interface TaskActionInterface {
  /**
   * Provide a description of the plugin.
   * @return string
   *   A string description of the plugin.
   */
  public function description();
}