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
abstract class SiteSettingsBase {

  /**
   * Site and environment settings.
   *
   * @var array
   */
  protected $settings;

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
  public function getVariable($name, $default = NULL) {
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
  public function getAllSettings() {
    if (!isset($this->settings)) {
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

      $this->settings = $settings;
    }
    return $this->settings;
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
   * Gets site settings.
   *
   * @return array
   */
  protected abstract function getSiteSettings();

}
