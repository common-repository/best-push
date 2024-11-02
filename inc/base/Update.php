<?php

/**
 * @package BestPush
 */

namespace BestPushInc\base;

use BestPushInc\base\BaseController;

class Update extends BaseController
{

    public $this_plugin;

    public function __construct()
    {
        parent::__construct();
        $this->this_plugin = 'best-push/best-push.php';

    }

    public function register()
    {
	    add_action('upgrader_process_complete', array($this, 'checkUpdateProcess'), 10, 2);

    }

    function checkUpdateProcess($upgrader_object, $hook_extra)
    {
        // https://wordpress.stackexchange.com/a/298671
        if (is_array($hook_extra) && array_key_exists('action', $hook_extra) && array_key_exists('type', $hook_extra) && array_key_exists('plugins', $hook_extra)) {
            // check first that array contain required keys to prevent undefined index error.
            if ($hook_extra['action'] == 'update' && $hook_extra['type'] == 'plugin' && is_array($hook_extra['plugins']) && !empty($hook_extra['plugins'])) {
                // if this action is update plugin.

                foreach ($hook_extra['plugins'] as $each_plugin) {
                    if ($each_plugin == $this->this_plugin) {
                        // if this plugin is in the updated plugins.
                        $this->processUpdate();
                    }
                } // endforeach;
                unset($each_plugin);
            } // endif update plugin and plugins not empty.
        }
    }

    private function processUpdate()
    {

        add_option(BaseController::$bell_options_group, array(
            'is_enabled' => false,
            'backgroundColor' => '#0647ff',
            'bellColor' => '#ffffff',
        ));
    }
}
