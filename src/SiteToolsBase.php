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
abstract class SiteToolsBase {

  /**
   * Site and environment settings.
   *
   * @var array
   */
  static $settings;

  /**
   * Returns a setting.
   *
   * Settings can be set in settings.php in the $settings array and requested
   * by this function. Settings should be used over configuration for read-only,
   * possibly low bootstrap configuration that is environment specific.
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
    $settings = static::getAllSettings();
    return isset($settings[$name]) ? $settings[$name] : $default;
  }

  /**
   * Returns all the settins.
   *
   * - Site tools settings (variable)
   * - Project
   *
   * @return array
   *   All the settings.
   */
  public static function getAllSettings() {
    if (!isset(static::$settings)) {
      $settings = static::getSiteSettings();

      // Check variables.
      if (empty($settings['project_name'])) {
        throw \Exception("Project name (project_name) is not defined in settings.php");
      }
      if (empty($settings['site_env'])) {
        throw \Exception("Site environment (site_env) is not defined in settings.php");
      }

      // Add environment + project settings
      // Note environment settings can override project settings.
      $settings += static::getEnvironmentSettings($settings['project_name'], $settings['site_env']);
      $settings += static::getProjectSettings($settings['project_name']);

      static::$settings = $settings;
    }
    return static::$settings;
  }

  /**
   * Run multiple drush commands through drush invoke.
   *
   * @param array $commands
   *   Array of commands, each an array with [ name, arguments, options]
   *
   */
  public static function invokeCommands(array $commands) {
    $return = TRUE;

    foreach ($commands as $command) {
      $command = array_merge($command, [[], []]);
      list($name, $arguments, $opts) = $command;

      if ($opts) {
        // We need to use drush_invoke_process() for the options to work.
        $result = drush_invoke_process('@self', $name, $arguments, $opts);
        if (is_array($result) && !empty($result['error_status'])) {
          $result = FALSE;
        }
      }
      else {
        $result = drush_invoke($name, $arguments);
      }
      if ($result === FALSE) {
        drush_set_error(dt("Error running drush command: @command", ['@command' => $name]));
        $return = FALSE;
        break;
      }
    }

    return $return;
  }

  /**
   * Check modules before enabling them.
   */
  public static function checkModules(array $module_list) {
    $found = $missing = array();
    foreach ($module_list as $module) {
      if (static::moduleHelper()::getModuleData($module)) {
        $found[] = static::moduleHelper()::isModuleEnabled($module) ? "$module (enabled)" : "$module (disabled)";
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
      static::moduleHelper()::disableModules($disable);
    }
    else {
      drush_print("No extensions to disable.");
    }

    // Uninstall modules
    if ($uninstall = static::getModulesToUninstall($modules)) {
      static::moduleHelper()::uninstallModules($uninstall);
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
      $info = static::moduleHelper()::getModuleInfo($check_name);
      if ($info && !empty($info['disable'])) {
        foreach ($info['disable'] as $name) {
          if (static::moduleHelper()::isModuleEnabled($name)) {
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
      $info = static::moduleHelper()::getModuleInfo($check_name);
      if ($info && !empty($info['uninstall'])) {
        foreach ($info['uninstall'] as $name) {
          if (static::moduleHelper()::isModuleEnabled($name)) {
            $list[] = $name;
          }
        }
      }
    }

    return array_unique($list);
  }

  /**
   * Get project environment settings.
   *
   * @return array
   */
  protected static function getProjectSettings($project_name) {
    $options = drush_get_option('project_settings', array());
    if (isset($options[$project_name])) {
      return $options[$project_name];
    }
    else {
      return array();
    }
  }

  /**
   * Get project environment settings.
   *
   * @return array
   */
  protected static function getEnvironmentSettings($project_name, $env_name = NULL) {
    $environments = drush_get_option('project_environments', array());

    if (isset($environments[$project_name])) {
      $project_environments = $environments[$project_name];
      if ($env_name) {
        return isset($project_environments[$env_name]) ? $project_environments[$env_name] : array();
      }
      else {
        return $project_environments;
      }
    }

    return array();
  }


  /**
   * Invoke drush site update commands.
   *
   * These are different for D7 and D8.
   *
   * @return boolean
   * TRUE if successful.
   */
  public static abstract function invokeSiteUpdateCommands();

  /**
   * Gets site settings.
   *
   * @return array
   */
  protected static abstract function getSiteSettings();

  /**
   * Gets module helper.
   *
   * @return string
   *   Class name (\Drupal\site_tools\ModuleHelperBase)
   */
  protected static abstract function moduleHelper();

}
