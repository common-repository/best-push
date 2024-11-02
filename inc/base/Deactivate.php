<?php
/**
 * @package BestPush
 */

namespace BestPushInc\base;

class Deactivate
{
    public static function deactivate()
    {
        $admin_email = get_option('admin_email');
        $args = array('body' => array(
            'email' => $admin_email,
            'domain' => get_site_url()
        ));
        $url = BaseController::$base_url . '/plugin/wordpress/deactivate_plugin';
        wp_remote_post($url, $args);
    }
}
