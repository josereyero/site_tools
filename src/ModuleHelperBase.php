<?php

namespace Drupal\site_tools;

/**
 * Base class for Drupal version independent module API.
 *
 * @ingroup utility
 */
abstract class ModuleHelperBase {

  /**
   * Module information.
   *
   * Returned by system_rebuild_module_data().
   *
   * @var array().
   */
  static $module_data;

  /**
   * Get module data.
   *
   * @param string $name
   *   Optional extension name.
   *
   * @return mixed
   *   Extension object if module name is provided. List of extensions otherwise.
   */
  public static function getModuleData($name = NULL) {
    if (!isset(static::$module_data)) {
      static::$module_data = system_rebuild_module_data();
    }
    if ($name) {
      return isset(static::$module_data[$name]) ? static::$module_data[$name] : NULL;
    }
    else {
      return static::$module_data[$name];
    }
  }

  /**
   * Get all module data.
   */
  protected static function resetModuleData() {
    unset(static::$module_data);
    //system_rebuild_module_data();
  }

  /**
   * Checks whether module is installed.
   *
   * @param string $name
   *   Module name.
   *
   * @return boolean
   *   TRUE if installed.
   */
  public static abstract function isModuleInstalled($name);

  /**
   * Checks whether module is enabled.
   *
    * @param string $name
   *   Module name.
   *
   * @return boolean
   *   TRUE if enabled.
   */
  public static abstract function isModuleEnabled($name);

  /**
   * Enable modules.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public static abstract function enableModules(array $module_list);

  /**
   * Disable modules.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public static abstract function disableModules(array $module_list);


  /**
   * Uninstall modules.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public static abstract function uninstallModules(array $module_list);

  /**
   * Gets module info array.
   *
   * @return array
   */
  public static abstract function getModuleInfo($name);
}