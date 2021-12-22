<?php

/**
 * Plugin Name: Modularity templates
 * Plugin URI: -
 * Description: Adds option to duplicate posts and their modules
 * Version: 0.2.0
 * Author: Whitespace
 * Author URI: https://www.whitespace.se/
 */


define('PLUGIN_PATH', plugin_basename(dirname(__FILE__)));


/** Load the text domain */
load_muplugin_textdomain('modularity-templates', PLUGIN_PATH . '/languages');




// Start application
add_action('plugins_loaded', function () {

    /** Initiate the application */
    new Municipio\WP\ModularityTemplates\Initializer\App();
}, 20);
