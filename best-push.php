<?php
/**
 * @package BestPush
 */

/*
 * Plugin Name: best push
 * Description: Web push notification plugin for wordpress.
 * version: 1.0.9
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: best-push
*/


defined('ABSPATH') or die('You cannot access this file!');

// Require once the Composer autoload
if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once(dirname(__FILE__) . '/vendor/autoload.php');
}

/**
 * Codes run during activation of the plugin
 */
function activate_best_push_plugin()
{
    BestPushInc\base\Activate::activate();
}

register_activation_hook(__FILE__, 'activate_best_push_plugin');


/**
 * Codes run during deactivation of the plugin
 */
function deactivate_best_push_plugin()
{
    BestPushInc\base\Deactivate::deactivate();
}

register_deactivation_hook(__FILE__, 'deactivate_best_push_plugin');


/**
 * Initialize all the core classes of the plugin
 */
if (class_exists('BestPushInc\\Init')) {
    BestPushInc\Init::register_services();
}
