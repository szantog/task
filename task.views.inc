<?php

/**
 * @file
 * Views area plugin info for task.
 */

/**
 * Implements hook_views_data_alter().
 */
function task_views_data_alter(array &$data) {
  // Field Plugins
  $data['task']['task_options'] = [
    'title' => t('Render Task Options'),
    'help' => t('Render the task options links.'),
    'field' => ['id' => 'task_options'],
  ];
  $data['task']['task_assigner'] = [
    'title' => t('Render Task Assigner'),
    'help' => t('Render the assigner using the assigned_by and assigned_by_type data.'),
    'field' => ['id' => 'task_assigner'],
  ];
  $data['task']['task_assignee'] = [
    'title' => t('Render Task Assignee'),
    'help' => t('Render the assigner using the assigned_to and assigned_to_type data.'),
    'field' => ['id' => 'task_assignee'],
  ];
}
