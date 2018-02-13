<?php

/**
 * @file
 *
 * Drush site tools - Reyero.net
 *
 * Provides some tool to enable disable additional modules defined in settings.php
 */

/**
 * Implements hook_drush_command().
 */
function site_tools_drush_command() {
  $items['site-enable-master'] = [
      'description' => 'Enable site master modules defined in project configuration',
      //'core' => ['8+'],
  ];
  $items['site-enable-environment'] = [
      'description' => 'Enable site environment modules, defined in project environment',
      //'core' => ['8+'],
  ];

  $items['site-update'] = [
      'description' => 'Update site, enable master and environment modules, revert features',
      //'core' => ['8+'],
  ];

  $items['site-backup'] = [
      'description' => 'Back-up site',
      //'core' => ['8+'],
  ];

  $items['site-reload'] = [
    'description' => 'Reload target site from source site and run site-update',
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_ROOT,
  ];

  return $items;
}

/**
 * Drush command: Enable master module.
 */
function drush_site_tools_site_enable_master() {
  _drush_site_tools_include();
  drush_print(dt("Checking configuration for project: @project", ['@project' => SiteTools::get('project_name')]));
  if ($modules = SiteTools::get('master_modules')) {
    if (SiteTools::checkModules($modules)) {
      ModuleHelper::enableModules($modules);
      SiteTools::checkDependencies($modules);
    }
  }
  else {
    return drush_set_error('SITE_NOT_FOUND', "No site master modules found.");
  }
}

/**
 * Drush command: Enable environment module.
 *
 * The environment name must be defined in $settings['environment']
 */
function drush_site_tools_site_enable_environment() {
  _drush_site_tools_include();
  drush_print(dt("Checking environment configuration for: @project/@environment", ['@project' => SiteTools::get('project_name'), '@environment' => SiteTools::get('site_env')]));
  if ($modules = SiteTools::get('environment_modules')) {
    if (SiteTools::checkModules($modules)) {
      ModuleHelper::enableModules($modules);
      SiteTools::checkDependencies($modules);
    }
  }
  else {
    return drush_set_error('SITE_NOT_FOUND', "No site environment modules found.");
  }
}

/**
 * Drush command callback: site-update
 *
 * - Clear caches
 * - Run db updates
 * - Enable master and environment modules.
 * - Revert features.
 */
function drush_site_tools_site_update() {
  _drush_site_tools_include();

  if (SiteTools::invokeSiteUpdateCommands()) {
    drush_print();
    drush_print("Site update completed.");
  }
}

/**
 * Drush command callback: site-reload
 *
 * Invoke drush project-reload
 */
function drush_site_tools_site_reload() {
  _drush_site_tools_include();
  $project_name = SiteTools::get('project_name');
  $site_env = SiteTools::get('site_env');

  drush_invoke('project-reload', [$project_name, $site_env]);
}

/**
 * Drush command callback: site-reload
 *
 * Invoke drush project-backup
 */
function drush_site_tools_site_backup() {
  _drush_site_tools_include();
  $project_name = SiteTools::get('project_name');
  $site_env = SiteTools::get('site_env');

  drush_invoke('project-backup', [$project_name, $site_env]);
}

/**
 * Include libraries for this version.
 */
function _drush_site_tools_include() {
  $version = 'drupal' . drush_drupal_major_version();
  require_once __DIR__ . '/site_tools.' . $version . '.inc';
}
