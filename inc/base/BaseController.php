<?php
/**
 * @package BestPush
 */

namespace BestPushInc\base;

class BaseController
{
    public $plugin_path; // Has "/" at the end
    public $plugin_url; // Has "/" at the end
    public $plugin;
    public static $base_url = "https://api.bestpush.io/v2";
    public static $settings_group = 'best_push_settings';
    public static $modal_options_group = 'best_push_modal_options';
    public static $bell_options_group = 'best_push_bell_options';
    public static $webpush_enabled_key = 'webpush_enabled';

    function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(dirname(__FILE__)));
        $this->plugin_url = plugin_dir_url(dirname(dirname(__FILE__)));
        $this->plugin = plugin_basename(dirname(dirname(dirname(__FILE__))) . '/best-push.php');
    }

    function localize_script($handler, $params)
    {
        wp_localize_script($handler, 'php_data', $params);
    }


    public function enqueueBundle()
    {
        wp_enqueue_script(
            'best_push_bundle',
            $this->plugin_url . 'assets/bundle.js',
            array('best_push_initialize_scripts'), // This assure that this script is load after webpush subscirbe script
            false,
            true
        );

        $settings = $this->get_settings(BaseController::$settings_group);
        $token = isset($settings['token']) ? 'Token ' . $settings['token'] : '';
        $app_id = isset($settings['app_id']) ? $settings['app_id'] : '';
        $this->localize_script(
            'best_push_bundle',
            json_encode(array(
                    'isActivated' => boolval($token),
                    'baseUrl' => BaseController::$base_url,
                    'userBaseUrl' => get_site_url(),
                    'adminUrl' => admin_url('admin.php'),
                    'ajaxUrl' => admin_url('admin-ajax.php'),
                    'headers' => array(

                        "Authorization" => $token,
                        "Accept" => "application/json"
                    ),
                    'app_id' => $app_id,
                    'email' => isset($settings['email']) ? $settings['email'] : '',
                    'strings' => array(
                        'resend' => esc_html__("Resend", "best-push"),
                        'delete' => esc_html__("Delete", "best-push")
                    ),
                    'settings' => $settings,
                    'modalOptions' => $this->filter_options($this->get_settings(BaseController::$modal_options_group)),
                    'bellOptions' => $this->filter_options($this->get_settings(BaseController::$bell_options_group)),
                )
            ));
    }

    public function get_settings($key)
    {
        $settings = get_option($key, array());
        if (!is_array($settings)) {
            $settings = array();
        }
        return $settings;
    }

    public function filter_options($options) {
        $result = array();

        if (!is_array($options)) {
            return $result;
        }

        foreach ($options  as $key => $value) {
            $result[$key] = stripslashes($value);
        }

        return $result;
    }

}