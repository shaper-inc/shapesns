<?php

/**
 * Plugin Name:     Shapesns
 * Plugin URI:      https://github.com/shaper-inc/shapesns
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          shpare-inc:
 * Author URI:      https://github.com/shaper-inc/
 * Text Domain:     shapesns
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Shapesns
 */

// Your code starts here.

add_action(
    'plugins_loaded',
    function () {
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "bootstrap.php";
        $app = ClassLoader::app_instance('\\ShapeSns\\App');
    }
);
