<?php

namespace Drupal\site_tools\Commands;

use Drush\Drush;
use Drush\Commands\DrushCommands;
use Drush\Utils\StringUtils;
use Drupal\site_tools\SiteTools;
use Consolidation\SiteAlias\SiteAliasManagerAwareTrait;
use Consolidation\SiteAlias\SiteAliasManagerAwareInterface;
use Consolidation\Config\ConfigAwareTrait;
use Reyero\DrupalTools\Drush\ProcessHelperCommandsTrait;
use Reyero\DrupalTools\Drush\ProjectInfoCommandsTrait;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class SiteToolsCommands extends DrushCommands implements SiteAliasManagerAwareInterface {
  use SiteAliasManagerAwareTrait;
  use ProcessHelperCommandsTrait, ProjectInfoCommandsTrait;

  /**
   * Enable site master modules defined in project configuration
   *
   *
   * @command site:enable-master
   * @aliases site-enable-master
   */
  public function enableMaster() {
    $this->output()->writeln(dt("Checking configuration for project: @project", ['@project' => SiteTools::get('project_name')]));
    if ($modules = $this->getSiteSettings()->get('master_modules')) {
      if (SiteTools::checkModules($modules)) {
        $enabled = SiteTools::moduleHelper()->enableModules($modules, TRUE);
        if ($enabled) {
          $this->output()->writeln(dt("Enabled modules: @list", ['@list' => implode(', ', $enabled)]));
        }
        else {
          $this->output()->writeln("No extensions to enable.");
        }
        SiteTools::checkDependencies($modules);
      }
    }
    else {
      throw new \Exception("No site master modules found.");
    }
  }

  /**
   * Enable site environment modules, defined in project environment
   *
   *
   * @command site:enable-environment
   * @aliases site-enable-environment
   */
  public function enableEnvironment() {

    $this->output()->writeln(dt("Checking environment configuration for: @project/@environment", ['@project' => SiteTools::get('project_name'), '@environment' => SiteTools::get('site_env')]));
    if ($modules = $this->getSiteSettings()->get('environment_modules')) {
      if (SiteTools::checkModules($modules)) {
        $enabled = SiteTools::moduleHelper()->enableModules($modules);
        if ($enabled) {
          $this->output()->writeln(dt("Enabled modules: @list", ['@list' => implode(', ', $enabled)]));
        } else {
            $this->output()->writeln("No extensions to enable.");
          }
        SiteTools::checkDependencies($modules);
      }
    }
    else {
      throw new \Exception("No site environment modules found.");
    }
  }

  /**
   * Update site, enable master and environment modules, revert features
   *
   * @command site:update
   * @aliases site-update
   */
  public function siteUpdate() {
    $commands = array();

    // Clear cache and run updates.
    $commands[] = ['cache-rebuild'];

    // Enable site master and environment modules.
    $commands[] = ['site-enable-master'];
    $commands[] = ['site-enable-environment'];

    // Update and config-import
    $commands[] = ['updatedb'];
    $commands[] = ['config-import', ['sync']];

    $this->runDrushCommandList($commands);
  }

  /**
   * Run site script from drupal root passing this site's alias.
   *
   * @param string $name
   *
   * @command site:script
   * @aliases site-script
   */
  public function siteScript($name) {
    $scripts_path = $this->getScriptsPath();
    $site_alias = $this->getSiteSettings()->getSiteAlias();
    foreach (['', '.sh', '.py'] as $extension) {
      $script = $scripts_path . '/' . $name . $extension;
      if (file_exists($script)) {
        $this->runShellCommand("$script $site_alias", $this->getDrupalRoot());
        return;
      }
    }
    throw new \Exception(sprintf("Script %s not found.", $script));
  }


  /**
   * Back-up site
   *
   *
   * @command site:backup
   * @aliases site-backup
   */
  public function backup() {
    $project_name = SiteTools::get('project_name');
    $env_name = SiteTools::get('site_env');
    $backup_dir = SiteTools::get('backup_directory');
    $site_alias = SiteTools::get('site_alias');

    $file_name = $project_name . '-' . $env_name . '-' . date('Ymd-His') . '.sql';

    $backup_path = "$backup_dir/$file_name";

    $variables = [
        '@project' => $project_name,
        '@site' => $site_alias,
        '@environment' => $env_name,
        '@backup_path' => $backup_path,
    ];

    $this->printMessage("Backing-up @project/@environment, site @site to: @backup_path", $variables);

    $options = [
        "result-file" => $backup_path,
        'structure-tables-key' => 'common',
        'gzip' => TRUE,
    ];

    $this->runDrushCommand("sql-dump", [], $options);

    $this->output()->writeln(sprintf("Backup Done, created: %s", "$backup_path.gz"));

    $latest_file = $project_name . '-' . $env_name . '-latest.sql.gz';

    $this->output()->writeln(sprintf("Creating / updating symlink from %s", $latest_path));

    $this->runShellCommand("ln -s -f $file_name.gz $latest_file", $backup_dir);
  }

  /**
   * Enable / disable maintenance mode
   *
   * @param int $value
   *   Enable (1) or disable (0)
   *
   * @command site:set-maintenance-mode
   * @aliases site-set-maintenance-mode
   */
  public function setMaintenanceMode($value = 1) {
    return $this->runDrushCommand('state-set', ['system.maintenance_mode', $value ? 1 : 0]);
  }

  /**
   * Reload target site from source site.
   *
   *
   * @command site:reload
   * @aliases site-reload
   */
  public function reload() {
    // See bottom of https://weitzman.github.io/blog/port-to-drush9 for details on what to change when porting a
    // legacy command.
    throw new \Exception("Command not implemented");
  }

  /**
   * Test global commands
   *
   * @command site:info
   * @aliases site-info
   *
   */
  public function siteInfo() {
    $settings = $this->getSiteSettings();
    $variables = [
      '@project' => $settings->getProjectName(),
      '@site_alias' => $settings->getSiteAlias(),
      '@environment' => $settings->getSiteEnv(),
    ];

    $this->printMessage("Site @project/@environment @site_alias", $variables);
    $this->output()->writeln(print_r($settings->getAll(), TRUE));
  }

  /**
   * Gets site settings.
   *
   * @return \Drupal\site_tools\SiteSettingsInterface
   */
  protected function getSiteSettings() {
    return SiteTools::siteSettings();
  }

}
