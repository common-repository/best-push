<?php
/**
 * @package BestPush
 */

namespace BestPushInc\api\callbacks;

use BestPushInc\base\BaseController;

class AnalyticsCallbacks extends BaseController
{
    public function render()
    {
        return require_once($this->plugin_path . "templates/basePage.php");
    }

}