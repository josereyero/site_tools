<?php

namespace Drupal\site_tools\drupal7;

use Drupal\site_tools\SiteSettingsBase;

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
    return variable_get('site_tools', array());
  }
}
