<?php

namespace Drupal\site_tools\drupal8;

use Drupal\site_tools\ModuleHelperBase;

/**
 * Drupal 8 Module handling API
 */
class ModuleHelper extends ModuleHelperBase {

  /**
   * Module info data.
   *
   * Returned by system_rebuild_module_data().
   *
   * @var array().
   */
  static $module_info;

  /**
   * Enable modules.
   */
  /*
  public static function enableModules(array $module_list) {
    \Drupal::service('module_installer')->install($module_list);
    static::resetModuleData();
  }
  */

  /**
   * Disable modules.
   */
  public static function disableModules(array $module_list) {
    // Modules cannot be really disabled in D8
    //module_disable($module_list);
  }

  /**
   * Uninstall modules.
   */
  public static function uninstallModules(array $module_list) {
    \Drupal::service('module_installer')->uninstall($module_list);
    static::resetModuleData();
  }

  /**
   * Checks whether module is installed.
   */
  public static function isModuleInstalled($name) {
    try {
      return (boolean)\Drupal::moduleHandler()->getModule($name);
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Checks whether module is installed.
   */
  public static function isModuleEnabled($name) {
    return static::isModuleInstalled($name);
  }

  /**
   * Gets module info array.
   */
  public static function getModuleInfo($name) {
    if (!isset(static::$module_info[$name])) {
      if ($extension = static::getModuleData($name)) {
        static::$module_info[$name] = \Drupal::service('info_parser')->parse($extension->getPathname());
      }
    }
    return static::$module_info[$name];
  }

}