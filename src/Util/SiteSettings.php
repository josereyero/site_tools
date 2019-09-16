<?php

namespace Drupal\site_tools\Util;

use Drupal\Core\Site\Settings;
use Drupal\site_tools\SiteSettingsInterface;
use Reyero\DrupalTools\ProjectTools;
use Reyero\DrupalTools\Settings\SettingsBase;

/**
 * Read only settings that are initialized with the class.
 *
 * Get settings the Drupal 8 way
 *
 * Define settings in settings.php
 *
 * $settings['site_tools'] = [
 *   'name' =>  'value'
 * ];
 *
 * @ingroup utility
 */
class SiteSettings extends SettingsBase implements SiteSettingsInterface  {

  /**
   * Initialize and build settings.
   */
  public function __construct() {
    $settings = Settings::get('site_tools', array());

    // Check variables.
    if (empty($settings['project_name'])) {
      throw new \Exception("Project name (project_name) is not defined in settings.php");
    }
    if (empty($settings['site_env'])) {
      throw new \Exception("Site environment (site_env) is not defined in settings.php");
    }

    // Add environment + project settings
    // Note environment settings can override project settings.
    $settings += ProjectTools::getEnvironment($settings['project_name'], $settings['site_env']);
    $settings += ProjectTools::getProject($settings['project_name']);

    $this->settings = $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function getProjectName() {
    return $this->get('project_name', '');
  }

  /**
   * {@inheritdoc}
   */
  public function getSiteEnv() {
    return $this->get('site_env', '');
  }

  /**
   * {@inheritdoc}
   */
  public function getSiteAlias() {
    return $this->get('site_alias', '');
  }


}
