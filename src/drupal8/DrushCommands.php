<?php

namespace Drupal\site_tools\drupal8;

use Drupal\site_tools\DrushCommandsBase;
use Drupal\site_tools\SiteTools;

/**
 * Drupal 8 - Site Tools API.
 */
class DrushCommands extends DrushCommandsBase {

  /**
   * Get commands to set maintenance mode.
   *
   * @param boolean $value
   *   Enable (1) / Disable (0)
   * @return array
   *   Drush commands.
   */
  public static function setMaintenanceMode($value) {
    $value = $value ? '1' : '0';
    return drush_invoke('state-set', ['system.maintenance_mode', $value]);
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
    $commands[] = ['cache-rebuild'];
    $commands[] = ['updatedb'];
    $commands[] = ['entity-updates'];

    // Enable site master and environment modules.
    $commands[] = ['site-enable-master'];
    $commands[] = ['site-enable-environment'];

    // Revert features.
    //$commands['features-revert-all'] = [];
    foreach (SiteTools::get('features_bundles') as $bundle) {
      $commands[] = ['features-import-all', [], ['--bundle=' . $bundle]];
    }

    return static::invokeCommands($commands);
  }
}
