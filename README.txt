Site Tools
=============

Introduction
------------
This is a collection of development and deployment tools to use with Drupal and Drush.
It provides some drush commands:

* site:enable-master Enables master site modules, see configuration below
* site:enable-environment Enables site environment modules, see configuration below
* site:backup Creates a backup in a predefined folder.
* site:update Runs all site update scripts (enable/disable modules, update database, clear caches, resync configuration, etc..)

This module can work together, but doesn't require, Project Tools
@see https://github.com/josereyero/project_tools

This is a development tool by https://reyero.net
Use at your own risk.

Requirements
------------
* Drupal 8, https://drupal.org
* Drush 9.x, http://docs.drush.org/en/9.x/
* Drupal Tools 8.x, https://github.com/josereyero/drupal-tools

Installation
------------

* Via composer:
  composer require reyero/site_tools

* Manual download:
  Place the module in your contributed modules folder.
  
Configuration
-------------

The settings to define in the site's settings.php file:

  $settings['project_name'] = 'EXAMPLE_NAME';
  $settings['site_env'] = 'EXAMPLE_ENV';
  
  // These additional settings can be defined by project_tools instead.
  
  // Drush alias for this site
  $settings['site_alias'] = '@SITEALIAS';
  
  // List of 'master' modules that will be enabled for all sites.
  $settings['master_modules'] = ['site_gronze_master'];
  // List of 'environment modules' that will be enabled only for this environment.
  $settings['environment_modules'] => ['site_gronze_devel'];

These modules can define an aditional section in their .info.yml file for
disabling or uninstalling other extensions.

Note that with Drupal 7 modules must be disabled before uninstalled.

  disable:
    - module1
    - module2

  uninstall:
    - module1
    - module2
