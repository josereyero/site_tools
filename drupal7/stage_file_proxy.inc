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
if (isset($conf['site_tools']['project_name']) && isset($conf['site_tools']['site_env']) && isset($conf['project_settings']) && isset($conf['project_environments'])) {
  $project_name = $conf['site_tools']['project_name'];
  $site_env = $conf['site_tools']['site_env'];

  // Get settings for current project and current environment.
  $current_environments = $conf['project_environments'][$project_name];
  $current_project = $conf['project_settings'][$project_name];
  $current_env = $current_environments[$site_env] + $current_project;

  // Get files from 'download_source' environment.
  if (isset($current_env['files_source'])) {
    $source_env = $current_environments[$current_env['files_source']] + $current_project;
    $conf['stage_file_proxy_origin'] = $source_env['base_url'];
    if (isset($source_env['file_public_path'])) {
      $conf['stage_file_proxy_origin_dir'] = $source_env['file_public_path']; //'sites/default/files';
    }
  }

}
