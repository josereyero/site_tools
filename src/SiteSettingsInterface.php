<?php

namespace Drupal\site_tools;

use Reyero\DrupalTools\Settings\SettingsInterface;

/**
 * Interface for site settings.
 *
 * @ingroup utility
 */
interface SiteSettingsInterface extends SettingsInterface {
  /**
   * Gets project name
   *
   * @return string
   */
  public function getProjectName();

  /**
   * Gets environment name
   *
   * @return string
   */
  public function getSiteEnv();

  /**
   * Gets drush site alias
   *
   * @return string
   */
  public function getSiteAlias();
}
