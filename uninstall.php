<?php

/**
 * Trigger this when uninstall plugin
 *
 * @package BestPush
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('best_push_settings');
delete_option('best_push_modal_options');