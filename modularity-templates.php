<?php

/**
 * Plugin Name: Modularity templates
 * Plugin URI: -
 * Description: Adds option to duplicate posts and their modules
 * Version: 1.0.0
 * Author: Whitespace
 * Author URI: https://www.whitespace.se/
 */

define("MODULARITY_TEMPLATES_PLUGIN_FILE", __FILE__);
define("MODULARITY_TEMPLATES_PATH", dirname(__FILE__));
define(
  "MODULARITY_TEMPLATES_AUTOLOAD_PATH",
  MODULARITY_TEMPLATES_PATH . "/autoload",
);

add_action("init", function () {
  $path = plugin_basename(dirname(__FILE__)) . "/languages";
  load_plugin_textdomain("modularity-templates", false, $path);
  load_muplugin_textdomain("modularity-templates", $path);
});

array_map(static function () {
  include_once func_get_args()[0];
}, glob(MODULARITY_TEMPLATES_AUTOLOAD_PATH . "/*.php"));

// Start application
add_action(
  "plugins_loaded",
  function () {
    /** Initiate the application */
    new Municipio\WP\ModularityTemplates\Initializer\App();
  },
  20,
);
