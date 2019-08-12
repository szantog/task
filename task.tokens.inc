<?php

/**
 * @file
 * Builds placeholder replacement tokens for task-related data.
 */

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\task\TaskUtilities;

/**
 * Implements hook_token_info().
 */
function task_token_info() {
  $types['task'] = [
    'name' => t('Tasks'),
    'description' => t('Tokens related to individual task accounts.'),
    'needs-data' => 'task',
  ];

  $task['options-links'] = [
    'name' => t('Task Options Links'),
    'description' => t("Renders the options links for the Task."),
  ];
  $task['url'] = [
    'name' => t("URL"),
    'description' => t("The URL of the account profile page."),
  ];
  
  $task['created'] = [
    'name' => t("Created"),
    'description' => t("The date the task account was created."),
    'type' => 'date',
  ];

  return [
    'types' => $types,
    'tokens' => ['task' => $task],
  ];
}

/**
 * Implements hook_tokens().
 */
function task_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {

  $token_service = \Drupal::token();
  $url_options = ['absolute' => TRUE];
  if (isset($options['langcode'])) {
    $url_options['language'] = \Drupal::languageManager()->getLanguage($options['langcode']);
    $langcode = $options['langcode'];
  }
  else {
    $langcode = NULL;
  }
  $replacements = [];
  if ($type == 'task' && !empty($data['task'])) {
    /** @var \Drupal\task\Entity\TaskInterface $task */
    $task = $data['task'];
    foreach ($tokens as $name => $original) {
      switch ($name) {
        // Basic task information.
        case 'options-links':
          if ($task->id()) {
            $markup_array = TaskUtilities::getTaskOptions($task);
            $replacements[$original] = $markup_array['#markup'];
          }
          else {
            $replacements[$original] = t('options not yet determined');
          }
          break;


        case 'url':
          $replacements[$original] = $task->id() ? $task->toUrl('canonical', $url_options)->toString() : t('not yet assigned');
          break;

        case 'created':
          $date_format = DateFormat::load('medium');
          $bubbleable_metadata->addCacheableDependency($date_format);
          // In the case of task_presave the created date may not yet be set.
          $replacements[$original] = $task->getCreatedTime() ? \Drupal::service('date.formatter')->format($task->getCreatedTime(), 'medium', '', NULL, $langcode) : t('not yet created');
          break;
      }
    }

    if ($registered_tokens = $token_service->findWithPrefix($tokens, 'created')) {
      $replacements += $token_service->generate('date', $registered_tokens, ['date' => $task->getCreatedTime()], $options, $bubbleable_metadata);
    }
  }
  

  return $replacements;
}
