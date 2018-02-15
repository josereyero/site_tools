<?php

namespace Drupal\site_tools\drupal8;

use Drupal\site_tools\SiteSettingsBase;
use Drupal\Core\Site\Settings;

/**
 * Drupal 7 Site Tools API
 */
class SiteSettings extends SiteSettingsBase {

  /**
   * Gets site settings.
   *
   * @return array
   */
  protected function getSiteSettings() {
    return Settings::get('site_tools', array());
  }

}
