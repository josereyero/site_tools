<?php

namespace Drupal\site_tools;

use Drupal\site_tools\Util\ModuleHelper;
use Drupal\site_tools\Util\SiteSettings;

/**
 * Read only settings that are initialized with the class.
 *
 * Get settings the Drupal 8 way
 *
 * Define settings in settings.php
 *
 * $settings['site_tools'] = [
 *   'name' =>  'value'
 * ];
 *
 * @ingroup utility
 */
class SiteTools {

  /**
   * Site and environment settings.
   *
   * @var \Drupal\site_tools\SiteSettingsInterface
   */
  static $site_settings;

  /**
   * Module helper object.
   *
   * @var \Drupal\site_tools\Util\ModuleHelper
   */
  static $module_helper;

  /**
   * Check modules before enabling them.
   *
   * @return boolean
   *   TRUE if modules exist.
   */
  public static function checkModules(array $module_list) {
    $found = $missing = array();
    foreach ($module_list as $module) {
      if (static::moduleHelper()->getModuleData($module)) {
        $found[] = static::moduleHelper()->isModuleEnabled($module) ? "$module (enabled)" : "$module (disabled)";
      }
      else {
        $missing[] = $module;
      }
    }
    if ($found) {
      drush_print(dt("Site modules found: @modules", ['@modules' => implode(', ', $found)]));
    }
    if ($missing) {
      drush_set_error(dt("Site modules not found: @modules", ['@modules' => implode(', ', $missing)]));
      return FALSE;
    }
    else {
      return TRUE;
    }
  }

  /**
   * Check modules for disable and uninstall data.
   * Then disable and uninstall corresponding ones.
   */
  public static function checkDependencies(array $modules) {
    // Disable modules
    if ($disable = static::getModulesToDisable($modules)) {
      static::moduleHelper()->disableModules($disable);
    }
    else {
      drush_print("No extensions to disable.");
    }

    // Uninstall modules
    if ($uninstall = static::getModulesToUninstall($modules)) {
      static::moduleHelper()->uninstallModules($uninstall);
    }
    else {
      drush_print("No extensions to uninstall.");
    }
  }

  /**
   * Gets list of modules to disable from other modules info.
   *
   * @param array $check_modules
   *   List of modules to check for disable data.
   *
   * @return array
   *   List of modules to disable.
   */
  static function getModulesToDisable($check_modules) {
    $list = array();

    foreach ($check_modules as $check_name) {
      $info = static::moduleHelper()->getModuleInfo($check_name);
      if ($info && !empty($info['disable'])) {
        foreach ($info['disable'] as $name) {
          if (static::moduleHelper()->isModuleEnabled($name)) {
            $list[] = $name;
          }
        }
      }
    }

    return array_unique($list);
  }

  /**
   * Gets list of modules to uninstall from other modules info.
   *
   * @param array $check_modules
   *   List of modules to check for uninstall data.
   *
   * @return array
   *   List of modules to uninstall.
   */
  static function getModulesToUninstall($check_modules) {
    $list = array();

    foreach ($check_modules as $check_name) {
      $info = static::moduleHelper()->getModuleInfo($check_name);
      if ($info && !empty($info['uninstall'])) {
        foreach ($info['uninstall'] as $name) {
          if (static::moduleHelper()->isModuleEnabled($name)) {
            $list[] = $name;
          }
        }
      }
    }

    return array_unique($list);
  }

  /**
   * Gets site settings.
   *
   * @return  \Drupal\site_tools\SiteSettingsInterface
   */
  public static function siteSettings() {
    if (!isset(static::$site_settings)) {
      static::$site_settings = new SiteSettings();
    }
    return static::$site_settings;
  }

  /**
   * Gets module helper.
   *
   * @return \Drupal\site_tools\Util\ModuleHelper
   */
  public static function moduleHelper() {
    if (!isset(static::$module_helper)) {
      static::$module_helper = new ModuleHelper();
    }
    return static::$module_helper;
  }

}
