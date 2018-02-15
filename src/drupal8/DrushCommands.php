<?php

namespace Drupal\site_tools\drupal8;

use Drupal\site_tools\DrushCommandsBase;
use Drupal\site_tools\SiteTools;

/**
 * Drupal 8 - Site Tools API.
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
