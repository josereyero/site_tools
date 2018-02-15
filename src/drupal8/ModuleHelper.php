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
  protected $module_info;

  /**
   * Enable modules.
   */
  /*
  public function enableModules(array $module_list) {
    \Drupal::service('module_installer')->install($module_list);
    $this->resetModuleData();
  }
  */

  /**
   * Disable modules.
   */
  public function disableModules(array $module_list) {
    // Modules cannot be really disabled in D8
    //module_disable($module_list);
  }

  /**
   * Uninstall modules.
   */
  public function uninstallModules(array $module_list) {
    \Drupal::service('module_installer')->uninstall($module_list);
    $this->resetModuleData();
  }

  /**
   * Checks whether module is installed.
   */
  public function isModuleInstalled($name) {
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
  public function isModuleEnabled($name) {
    return $this->isModuleInstalled($name);
  }

  /**
   * Gets module info array.
   */
  public function getModuleInfo($name) {
    if (!isset($this->module_info[$name])) {
      if ($extension = $this->getModuleData($name)) {
        $this->module_info[$name] = \Drupal::service('info_parser')->parse($extension->getPathname());
      }
    }
    return $this->module_info[$name];
  }

}