<?php

namespace Drupal\site_tools\drupal7;

use Drupal\site_tools\DrushCommandsBase;

/**
 * Drupal 7 Site Tools API
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
    return drush_invoke('variable-set', ['maintenance_mode', $value]);
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
    // Revert features if module is enabled.
    if (module_exists('features')) {
      $commands[] = ['features-revert-all'];
    }
    return static::invokeCommands($commands);
  }
}
