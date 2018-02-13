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
  static function isModuleInstalled($name) {
    $module = static::getModuleData($name);
    return $module && $module->schema_version > -1;
  }

  /**
   * Checks whether module is installed.
   */
  static function isModuleEnabled($name) {
    $module = static::getModuleData($name);
    return $module && !empty($module->status);
  }

  /**
   * Enable modules.
   */
  /*
  public static function enableModules(array $module_list) {
    return module_enable($module_list);
  }
  */

  /**
   * Disable modules.
   */
  public static function disableModules(array $module_list) {
    return module_disable($module_list);
  }

  /**
   * Uninstall modules.
   */
  public static function uninstallModules(array $module_list) {
    return drupal_uninstall_modules($module_list);
  }

  /**
   * Gets module info array.
   */
  public static function getModuleInfo($name) {
    if ($extension = static::getModuleData($name)) {
      return $extension->info;
    }
  }
}