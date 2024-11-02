<?php
/**
 * @package BestPush
 */

namespace BestPushInc\api\callbacks;

use BestPushInc\base\BaseController;

class SettingsCallbacks extends BaseController
{
    public function render()
    {


        $url = BaseController::$base_url . '/plugin/login/activate/';
        $activation_key = isset($_GET['activation_key']) ? sanitize_text_field($_GET['activation_key']) : null;

        if ($activation_key) {

            $domain = str_replace("http://", "", get_site_url());
            $domain = str_replace('https://', '', $domain);
            $domain = str_replace('www.', '', $domain);

            $args = array('body' => array(
                'domain' => $domain,
                'activation_key' => $activation_key
            ));

            $response = wp_remote_post($url, $args);
            $result = wp_remote_retrieve_body($response);
            $json = json_decode($result, true);

            if ($json['token']) {

                $options = $this->get_settings(BaseController::$settings_group);
                $options['app_id'] = $json['app_id'];
                $options['token'] = $json['token'];
                $options[BaseController::$webpush_enabled_key] = true;
                update_option(BaseController::$settings_group, $options);
                $modal_options = $this->get_settings(BaseController::$modal_options_group);
                $modal_options['showDialog'] = true;
                $modal_options['position'] = 'top-center';
                update_option(BaseController::$modal_options_group, $modal_options);
            }
        }
        $this->enqueueBundle();
        return require_once($this->plugin_path . "templates/basePage.php");
    }

}