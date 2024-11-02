<?php
/**
 * @package BestPush
 */

namespace BestPushInc\pages;

use BestPushInc\api\callbacks\AnalyticsCallbacks;
use BestPushInc\api\callbacks\NewCallbacks;
use BestPushInc\api\callbacks\NotificationsCallbacks;
use BestPushInc\api\callbacks\SettingsCallbacks;
use BestPushInc\api\callbacks\SupportCallbacks;
use BestPushInc\api\SettingsApi;
use BestPushInc\base\BaseController;

class AdminPages extends BaseController
{
    public $settings;
    public $pages;
    public $subpages;

    public $settingsManager;
    private $icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA0MC43NSA0Ni43MSI+PHRpdGxlPkFzc2V0IDE8L3RpdGxlPjxnIGlkPSJMYXllcl8yIiBkYXRhLW5hbWU9IkxheWVyIDIiIHN0eWxlPSImIzEwOyAgICBmaWxsOiByZ2JhKDI0MCwyNDUsMjUwLC42KTsmIzEwOyI+PGcgaWQ9IkxheWVyXzEtMiIgZGF0YS1uYW1lPSJMYXllciAxIj48cGF0aCBkPSJNMzEuMTQsOC44N2E5LjYzLDkuNjMsMCwwLDAtOS42MSw5LjYydi4wNmgwVjM3LjA2aDMuN1YyNmE5LjUxLDkuNTEsMCwwLDAsNS45MSwyLjA5LDkuNjIsOS42MiwwLDAsMCwwLTE5LjIzWm0wLDE1LjUzYTUuOTEsNS45MSwwLDEsMSw1LjkxLTUuOTFBNS45Miw1LjkyLDAsMCwxLDMxLjE0LDI0LjRaIi8+PHBhdGggZD0iTTE1LjU3LDM3LjFIMTEuODZhOS42Myw5LjYzLDAsMCwwLDkuNjIsOS42MVY0M0E1LjkyLDUuOTIsMCwwLDEsMTUuNTcsMzcuMVoiLz48cGF0aCBkPSJNMTkuMjIsMTguNThBOS42Myw5LjYzLDAsMCwwLDkuNjEsOWE5LjQ4LDkuNDgsMCwwLDAtNS45LDIuMDlWMEgwVjE4LjUySDBzMCwwLDAsLjA2YTkuNjEsOS42MSwwLDEsMCwxOS4yMiwwWk05LjYxLDI0LjQ5YTUuOTEsNS45MSwwLDEsMSw1LjkxLTUuOTFBNS45Miw1LjkyLDAsMCwxLDkuNjEsMjQuNDlaIi8+PC9nPjwvZz48L3N2Zz4=';

    function __construct()
    {
        parent::__construct();

        $this->settings = new SettingsApi();
        $this->pages = array(
            array(
                'page_title' => 'New Message',
                'menu_title' => __('best push', 'best-push'),
                'capability' => 'manage_options',
                'menu_slug' => 'best_push_new',
                'callback' => array(new NewCallbacks(), 'render'),
                'icon_url' => $this->icon,
                'position' => 70
            ),
        );

        $this->subpages = array(

            array(
                'page_title' => 'Notifications',
                'menu_title' => __('Notifications', 'best-push'),
                'capability' => 'manage_options',
                'menu_slug' => 'best_push_notifications',
                'callback' => array(new NotificationsCallbacks(), 'render'),
                'icon_url' => 'dashicons-portfolio',
                'position' => 70
            ),
            array(
                'page_title' => 'Analytics',
                'menu_title' => __('Analytics', 'best-push'),
                'capability' => 'manage_options',
                'menu_slug' => 'best_push_analytics',
                'callback' => array(new AnalyticsCallbacks(), 'render'),
                'icon_url' => 'dashicons-portfolio',
                'position' => 70
            ),
            array(
                'page_title' => 'Settings',
                'menu_title' => __('Settings', 'best-push'),
                'capability' => 'manage_options',
                'menu_slug' => 'best_push_settings',
                'callback' => array(new SettingsCallbacks(), 'render'),
                'icon_url' => 'dashicons-portfolio',
                'position' => 70
            ),
            array(
                'page_title' => 'Support',
                'menu_title' => __('Support', 'best-push'),
                'capability' => 'manage_options',
                'menu_slug' => 'best_push_support',
                'callback' => array(new SupportCallbacks(), 'render'),
                'icon_url' => 'dashicons-portfolio',
                'position' => 70
            ),
        );

    }

    function register()
    {
        $this->settings->addPage($this->pages)->withSubPage(__('New Message', 'best-push'))->addSubPages($this->subpages)->register();
    }

}