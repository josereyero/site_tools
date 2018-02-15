<?php

namespace Drupal\site_tools;

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
   * @var \Drupal\site_tools\SiteSettingsBase
   */
  static $site_settings;

  /**
   * Module helper object.
   *
   * @var \Drupal\site_tools\ModuleHelperBase
   */
  static $module_helper;

  /**
   * Returns a variable / setting.
   *
   * @param string $name
   *   The name of the setting to return.
   * @param mixed $default
   *   (optional) The default value to use if this setting is not set.
   *
   * @return mixed
   *   The value of the setting, the provided default if not set.
   */
  public static function get($name, $default = NULL) {
    return static::siteSettings()->getVariable($name, $default);
  }

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
   * @return  \Drupal\site_tools\SiteSettingsBase
   */
  public static function siteSettings() {
    if (!isset(static::$site_settings)) {
      $class = static::getDrupalVersionClass('SiteSettings');
      static::$site_settings = new $class();
    }
    return static::$site_settings;
  }

  /**
   * Gets module helper.
   *
   * @return string
   *   Class name (\Drupal\site_tools\ModuleHelperBase)
   */
  public static function moduleHelper() {
    if (!isset(static::$module_helper)) {
      $class = static::getDrupalVersionClass('ModuleHelper');
      static::$module_helper = new $class();
    }
    return static::$module_helper;
  }

  /**
   * Gets Drush commands class
   *
   * @return string
   *   Class name
   */
  public static function drushCommands() {
    return static::getDrupalVersionClass('DrushCommands');
  }

  /**
   * Gets actual class name depending on Drupal version.
   *
   * @paramm string $class
   *   Class name without namespace.
   */
  protected static function getDrupalVersionClass($class_name) {
    if (defined("DRUPAL_CORE_COMPATIBILITY") && DRUPAL_CORE_COMPATIBILITY == '7.x') {
      return "\\Drupal\\site_tools\\drupal7\\" . $class_name;
    }
    else {
      return "\\Drupal\\site_tools\\drupal8\\" . $class_name;
    }
  }
}
