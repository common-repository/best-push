<?php
/**
 * @package BestPush
 */

namespace BestPushInc\base;

class Activate
{
    public static function activate()
    {
        add_option(BaseController::$modal_options_group, array(
            'showDialog' => true,
            'position' => 'top-center',
            'title' => 'Subscribe to notifications',
            'content' => 'We\'d like to show you notifications for the latest news and updates.',
            'acceptText' => 'Accept',
            'rejectText' => 'No Thanks',
        ));

        add_option(BaseController::$bell_options_group, array(
            'is_enabled' => false,
            'backgroundColor' => '#0647ff',
            'bellColor' => '#ffffff',
        ));

        $admin_email = get_option('admin_email');
        $args = array('body' => array(
            'email' => $admin_email,
            'domain' => get_site_url()
        ));
        $url = BaseController::$base_url . '/plugin/wordpress/activate_plugin';
        wp_remote_post($url, $args);
    }
}