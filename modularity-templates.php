<?php

/**
 * Plugin Name: Modularity templates
 * Plugin URI: -
 * Description: Turns your Wordpress site into a headless CMS
 * Version: 0.1.0
 * Author: Whitespace
 * Author URI: https://www.whitespace.se/
 */

define('PLUGIN_PATH', plugin_dir_path(__FILE__));




// Start application
add_action('plugins_loaded', function () {

    /** Load the text domain */
    load_muplugin_textdomain('modularity-templates', PLUGIN_PATH . '/languages');


    /** Initiate the application */
    new Municipio\WP\ModularityTemplates\Initializer\App();
}, 20);
