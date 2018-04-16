<?php

/**
 * Site tools. Include configuration for stage_file_proxy.
 *
 * This file must be included from devel settings.php.
 *
 * There are a few variables that must be defined for this to work:
 *
 * - $conf['site_tools']
 * - $conf['project_settings']
 * - $conf['project_environments']
 *
 * - $conf['project_environments'][PROJECT_NAME][SITE_ENV]['files_source']
 * - $conf['project_environments'][PROJECT_NAME][FILES_SOURCE]['base_url']
 * - $conf['project_environments'][PROJECT_NAME][FILES_SOURCE]['file_public_path']
 */

/**
 * Check whether site tools configuration is in place.
 *
 * @param boolean $throw_exception
 *   Throw exception is site tools not properly configured.
 *
 * @return boolean
 *   TRUE if project_name, site_env and project configuration exist.
 */
function _site_tools_check_settings($throw_exception = FALSE) {
  global $conf;
  if (isset($conf['site_tools']['project_name']) && isset($conf['site_tools']['site_env'])
      && isset($conf['project_settings']) && isset($conf['project_environments'])) {
      return TRUE;
  }
  elseif ($throw_exception) {
    // Throwing exception here with D7 is a no-go, gets error procesing it.
    print "Site tools not properly configured, check your settings.php\n";
    exit(1);
  }
  else {
    return FALSE;
  }
}

/**
 * Get project name end environment
 *
 * @return array
 *   Array with project name and environment.
 */
function _site_tools_get_project_environment() {
  global $conf;
  $project_name = $conf['site_tools']['project_name'];
  $site_env = $conf['site_tools']['site_env'];
  return array($project_name, $site_env);
}

/**
 * Get environment settings.
 *
 * @return array
 *   Settings for this project / environment.
 */
function _site_tools_get_env_settings($throw_exception = FALSE) {
  global $conf;
  if (_site_tools_check_settings($throw_exception)) {
    list($project_name, $site_env) = _site_tools_get_project_environment();
  }
  // Get settings for current project and current environment.
  $current_environments = $conf['project_environments'][$project_name];
  $current_project = $conf['project_settings'][$project_name];
  return $current_environments[$site_env] + $current_project;
}
