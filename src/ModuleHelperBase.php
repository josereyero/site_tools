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
  protected $module_data;

  /**
   * Enable modules.
   *
   * For some reason enabling already enabled modules through Drupal API in D8
   * doesn't enable dependencies.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public function enableModules(array $module_list) {
    $result = drush_invoke('pm-enable', $module_list);
    $this->resetModuleData();
    return $result;
  }

  /**
   * Get module data.
   *
   * @param string $name
   *   Optional extension name.
   *
   * @return mixed
   *   Extension object if module name is provided. List of extensions otherwise.
   */
  public function getModuleData($name = NULL) {
    if (!isset($this->module_data)) {
      $this->module_data = system_rebuild_module_data();
    }
    if ($name) {
      return isset($this->module_data[$name]) ? $this->module_data[$name] : NULL;
    }
    else {
      return $this->module_data[$name];
    }
  }

  /**
   * Get all module data.
   */
  protected function resetModuleData() {
    $this->module_data = NULL;
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
  public abstract function isModuleInstalled($name);

  /**
   * Checks whether module is enabled.
   *
    * @param string $name
   *   Module name.
   *
   * @return boolean
   *   TRUE if enabled.
   */
  public abstract function isModuleEnabled($name);


  /**
   * Disable modules.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public abstract function disableModules(array $module_list);


  /**
   * Uninstall modules.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public abstract function uninstallModules(array $module_list);

  /**
   * Gets module info array.
   *
   * @return array
   */
  public abstract function getModuleInfo($name);
}