<?php

/**
 * Set base URL depending on domain, environment.
 *
 * This file must be included from devel settings.php.
 */

require_once __DIR__ . '/site_tools.inc';

if ($env_settings = _site_tools_get_env_settings(TRUE)) {
  if (!empty($env_settings['base_url'])) {
    $base_url = $env_settings['base_url'];
  }
  elseif (!empty($env_settings['host'])) {
    $base_url = 'http://' . $env_settings['host'];
  }
}