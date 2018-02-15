<?php

namespace Drupal\site_tools\drupal7;

use Drupal\site_tools\ModuleHelperBase;

/**
 * Drupal 7 Module handling API
 */
class ModuleHelper extends ModuleHelperBase {
  /**
   * Checks whether module is installed.
   */
  public function isModuleInstalled($name) {
    $module = $this->getModuleData($name);
    return $module && $module->schema_version > -1;
  }

  /**
   * Checks whether module is installed.
   */
  public function isModuleEnabled($name) {
    $module = $this->getModuleData($name);
    return $module && !empty($module->status);
  }

  /**
   * Enable modules.
   */
  /*
  public function enableModules(array $module_list) {
    return module_enable($module_list);
  }
  */

  /**
   * Disable modules.
   */
  public function disableModules(array $module_list) {
    return module_disable($module_list);
  }

  /**
   * Uninstall modules.
   */
  public function uninstallModules(array $module_list) {
    return drupal_uninstall_modules($module_list);
  }

  /**
   * Gets module info array.
   */
  public function getModuleInfo($name) {
    if ($extension = $this->getModuleData($name)) {
      return $extension->info;
    }
  }
}