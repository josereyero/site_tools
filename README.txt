Site Tools
=============

Introduction
------------
This is a collection of development and deployment tools to use with Drupal and Drush.
It creates drush commands for:

* Running all site update scripts (update database, clear caches, revert configuration, etc..)

This module can work together but doesn't require Project Tools
@see https://github.com/josereyero/project_tools

This is a development tool by https://reyero.net
Use at your own risk.

Requirements
------------
* Drupal 7 or Drupal 8, https://drupal.org
* Drush 8.x, http://docs.drush.org/en/8.x/
* Features, https://www.drupal.org/project/features

Installation
------------

* Via composer:
  composer require reyero/site_tools

* Manual download:
  Place the module in your contributed modules folder.
  
Configuration
-------------

The settings to define in the site's settings.php file:

Examples are for Drupal 8, use '$conf' instead of $settings for Drupal 7 

  $settings['project_name'] = 'EXAMPLE_NAME';
  $settings['site_env'] = 'EXAMPLE_ENV';
  
  // These additional settings can be defined by project_tools instead.
  
  // Drush alias for this site
  $settings['site_alias'] = '@SITEALIAS';
  
  // List of 'master' modules that will be enabled for all sites.
  $settings['master_modules'] = ['site_gronze_master'];
  // List of 'environment modules' that will be enabled only for this environment.
  $settings['environment_modules'] => ['site_gronze_devel'];

These modules can define an aditional section in their .info file for
disabling or uninstalling other extensions.

Note that with Drupal 7 modules must be disabled before uninstalled.

  disable[] = module1
  disable[] = module2

  uninstall[] = module1
  uninstall[] = module2