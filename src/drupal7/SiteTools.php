<?php

namespace Drupal\site_tools\drupal7;

use Drupal\site_tools\SiteToolsBase;

/**
 * Drupal 7 Site Tools API
 */
class SiteTools extends SiteToolsBase {

  /**
   * Gets site settings.
   *
   * @return array
   */
  protected static function getSiteSettings() {
    return variable_get('site_tools', array());
  }

  /**
   * Gets module helper.
   *
   * @return string
   *   Class name (\Drupal\site_tools\ModuleHelperBase)
   */
  protected static function moduleHelper() {
    return '\Drupal\site_tools\drupal7\ModuleHelper';
  }

  /**
   * Invoke drush site update commands.
   *
   * These are different for D7 and D8.
   *
   * @return boolean
   * TRUE if successful.
   */
  public static function invokeSiteUpdateCommands() {
    $commands = array();

    // Clear cache and run updates.
    $commands[] = ['cache-clear', ['all']];
    $commands[] = ['updatedb'];

    // Enable site master and environment modules.
    $commands[] = ['site-enable-master'];
    $commands[] = ['site-enable-environment'];
    // Revert features.
    $commands[] = ['features-revert-all'];

    return static::invokeCommands($commands);
  }
}
