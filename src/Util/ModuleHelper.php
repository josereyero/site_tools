<?php

namespace Drupal\site_tools\Util;

use Drush\Drush;

/**
 * Base class for Drupal version independent module API.
 *
 * @ingroup utility
 */
class ModuleHelper {

  /**
   * Module information.
   *
   * Returned by system_rebuild_module_data().
   *
   * @var array().
   */
  protected $module_data;

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
   *
   * Force enable dependencies too.
   *
   * For some reason enabling already enabled modules through Drupal API in D8
   * doesn't enable dependencies. Drush 9 doesn't work for that either.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return array
   *   List of enabled modules.
   */
  public function enableModules(array $module_list, $enable_dependencies = TRUE) {
    if ($enable_dependencies && ($dependencies = $this->getModuleDependencies($module_list))) {
      $module_list = array_merge($module_list, $dependencies);
    }

    $enable_modules = $this->filterOutEnabled($module_list);

    if ($enable_modules) {
      $this->moduleInstaller()->install($enable_modules);
      $this->resetModuleData();
    }

    return $enable_modules;
  }

  /**
   * Get module dependencies.
   *
   * @param array $module_list
   *   List of module names to find dependencies for.
   *
   * @return array
   *   List of module dependency names
   */
  public function getModuleDependencies(array $module_list) {
    $list = array();
    foreach ($module_list as $module_name) {
      /* @var \Drupal\Core\Extension\Extension $extension */
      $extension = $this->getModuleData($module_name);
      foreach ($extension->requires as $dependency) {
        $list[] = $dependency->getName();
      }
    }
    return array_unique(array_diff($list, $module_list));
  }

  /**
   * Filter out enabled modules.
   *
   * @param $module_list
   *   Array of module names.
   *
   * @return array
   *
   */
  public function filterOutEnabled($module_list) {
    if ($enabled = array_filter($module_list, [$this, 'isModuleInstalled'])) {
      $module_list = array_diff($module_list, $enabled);
    }
    return $module_list;
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
   * Disable modules.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public function disableModules(array $module_list) {
    // Modules cannot be really disabled in D8
    //module_disable($module_list);
  }

  /**
   * Uninstall modules.
   *
   * @param array $module_list
   *   List of module names.
   *
   * @return boolean
   *   TRUE if successful.
   */
  public function uninstallModules(array $module_list) {
    $this->moduleInstaller()->uninstall($module_list);
    $this->resetModuleData();
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
  public function isModuleInstalled($name) {
    try {
      return (boolean)\Drupal::moduleHandler()->getModule($name);
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Checks whether module is enabled.
   *
   * @param string $name
   *   Module name.
   *
   * @return boolean
   *   TRUE if enabled.
   */
  public function isModuleEnabled($name) {
    return $this->isModuleInstalled($name);
  }


  /**
   * Gets module info array.
   *
   * @return array
   */
  public function getModuleInfo($name) {
    if (!isset($this->module_info[$name])) {
      if ($extension = $this->getModuleData($name)) {
        $this->module_info[$name] = \Drupal::service('info_parser')->parse($extension->getPathname());
      }
    }
    return $this->module_info[$name];
  }

  /**
   * Gets the module installer service.
   *
   * @return \Drupal\Core\Extension\ModuleInstallerInterface
   */
  public function moduleInstaller() {
    return \Drupal::service('module_installer');
  }
}