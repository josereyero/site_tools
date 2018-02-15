<?php

namespace Drupal\site_tools\drupal7;

use Drupal\site_tools\DrushCommandsBase;

/**
 * Drupal 7 Site Tools API
 */
class DrushCommands extends DrushCommandsBase {

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
