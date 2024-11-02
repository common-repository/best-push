<?php

/**
 * @package BestPush
 */

namespace BestPushInc\base;

class Enqueue extends BaseController
{
    private $settings_keys;

    function __construct()
    {
        $this->settings_keys = array(
            Enqueue::$settings_group => array(BaseController::$webpush_enabled_key, 'email'),
            Enqueue::$modal_options_group => array(
                'pages_not_to_show', 'pages_to_show', 'showDialog',
                'title', 'content', 'acceptText', 'rejectText', 'icon', 'position',
                'mobilePosition', 'dialogRetryRate', 'dialogDirection'
            ),
            Enqueue::$bell_options_group => array(
                'is_enabled', 'backgroundColor', 'bellColor'
            ),
        );
        parent::__construct();
    }

    function register()
    {
        add_action('admin_enqueue_scripts', array($this, 'bestpush_enqueue'));

        if (is_admin()) {
            add_action("wp_ajax_bestpush_save_general", array($this, "bestpush_save_general"));
            add_action("wp_ajax_bestpush_save_prompt", array($this, "bestpush_save_prompt"));
            add_action("wp_ajax_bestpush_save_bell", array($this, "bestpush_save_bell"));
        }
    }

    function bestpush_save_general()
    {
        $this->update_partial_settings($_POST, BaseController::$settings_group);
        wp_die();
    }

    function bestpush_save_prompt()
    {
        $this->update_partial_settings($_POST, BaseController::$modal_options_group);
        wp_die();
    }

    function bestpush_save_bell()
    {
        $this->update_partial_settings($_POST, BaseController::$bell_options_group);
        wp_die();
    }

    function update_partial_settings($postData, $option_key)
    {
        if (!is_admin()) {
            return;
        }

        $availableKeys = $this->settings_keys[$option_key];
        $keyValueToUpdate = array();

        foreach ($postData as $key => $data) {
            if (in_array($key, $availableKeys)) {
                $keyValueToUpdate[$key] = sanitize_text_field($data);
            }
        }

        $current_settings = $this->get_settings($option_key);
        update_option($option_key,  array_merge($current_settings, $keyValueToUpdate));
    }

    /**
     * Add context to script
     * @param $handler
     * @param $params
     */
    function bestpush_enqueue()
    {
        $this->enqueueBundle();
    }
}
