<?php

namespace Drupal\site_tools;

/**
 * Drush Commands Helper base class
 */
abstract class DrushCommandsBase {
  /**
   * Run multiple drush commands through drush invoke.
   *
   * @param array $commands
   *   Array of commands, each an array with [ name, arguments, options]
   *
   */
  public static function invokeCommands(array $commands) {
    $return = TRUE;

    foreach ($commands as $command) {
      $command = array_merge($command, [[], []]);
      list($name, $arguments, $opts) = $command;

      drush_print(dt("Invoke: drush @name @arguments @options",[
          '@name' => $name,
          '@arguments' => implode(' ', $arguments),
          '@options' => implode(' ', $opts),
      ]));

      if ($opts) {
        // We need to use drush_invoke_process() for the options to work.
        $result = drush_invoke_process('@self', $name, $arguments, $opts);
        if (is_array($result) && !empty($result['error_status'])) {
          $result = FALSE;
        }
      }
      else {
        $result = drush_invoke($name, $arguments);
      }
      if ($result === FALSE) {
        drush_set_error(dt("Error running drush command: @command", ['@command' => $name]));
        $return = FALSE;
        break;
      }
      drush_print();
    }

    return $return;
  }
}