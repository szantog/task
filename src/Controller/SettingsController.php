<?php

namespace Drupal\task_api\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
* An settings controller.
*/
class SettingsController extends ControllerBase {

/**
* Returns a render-able array for a test page.
*/
public function settings() {
$build = [
'#markup' => $this->t('Settings Page'),
];
return $build;
}

}