<?php

/**
 * @package BestPush
 */

namespace BestPushInc\base;

use BestPushInc\base\BaseController;

class WebpushScripts extends BaseController
{
    public $best_push_settings;
    public $best_push_modal_options;
    public $webpush_enabled = true;

    public $service_worker;
    public $service_worker_dest;

    public function __construct()
    {
        parent::__construct();

        $this->best_push_settings = $this->get_settings(BaseController::$settings_group);
        $this->best_push_modal_options = $this->get_settings(BaseController::$modal_options_group);
        $this->best_push_bell_options = $this->get_settings(BaseController::$bell_options_group);

        $this->webpush_enabled = boolval($this->getSettingValue('webpush_enabled'));

        $this->service_worker = $this->plugin_path . "assets/bestpush-sw.js";
        $this->service_worker_dest = ABSPATH . "bestpush-sw.js";
    }

    public function register()
    {
        add_action('wp_footer', array($this, 'registerWebpushScriptsForSite'));
        add_action('admin_enqueue_scripts', array($this, 'registerWebpushScriptsForAdmin'));

        $this->attachServiceWorker();
    }

    public function attachServiceWorker()
    {
        if (!file_exists($this->service_worker_dest) and is_ssl()) {
            copy($this->service_worker, $this->service_worker_dest);
        }
    }

    private function getCurrentPageNumber()
    {
        if (is_page()) {
            $pageNumber = -1; // If we couldn't find the page number then return -1

            $pageId = isset($_GET['page_id']) ? sanitize_text_field($_GET['page_id']) : null;

            if ($pageId) {
                $pageNumber = (string)$pageId;
            }

            if (get_query_var('pagename') !== null) {
                $postObj = get_page_by_path(get_query_var('pagename'));
                if (isset($postObj->ID)) {
                    $pageNumber = (string)$postObj->ID;
                }
            }

            return $pageNumber;
        } else {
            // When it's the blog or it's a post
            $post = get_post();

            if (isset($post->ID)) {
                return (string)$post->ID;
            }

            return -1; // Means we couldn't find the page number
        }
    }

    private function shouldShowWebpush()
    {
        $pagesNotToShow = $this->getSettingValue('pages_not_to_show');
        $pagesToShow = $this->getSettingValue('pages_to_show');

        $pagesNotToShowArray = (isset($pagesNotToShow) & $pagesNotToShow !== "") ? preg_split("/[\s,]+/", $pagesNotToShow) : null;
        $pagesToShowArray = (isset($pagesToShow) & $pagesToShow !== "") ? preg_split("/[\s,]+/", $pagesToShow) : null;

        $currentPage = $this->getCurrentPageNumber();

        if ($currentPage !== -1 && $pagesToShowArray !== null) { // Pages To show has priority over pages not to show
            if (!in_array($currentPage, $pagesToShowArray)) {
                return false; // If current page is not in the array of pages to show then return function
            }
        } else if ($currentPage !== -1 && $pagesNotToShowArray !== null) {
            if (in_array($currentPage, $pagesNotToShowArray)) {
                return false; // if current page is in the array of pages not to show then return function
            }
        }

        return true;
    }

    public function registerWebpushScriptsForSite()
    {
        $this->registerWebpushScripts(false);
    }

    public function registerWebpushScriptsForAdmin()
    {
        $this->registerWebpushScripts(true);
    }

    private function registerWebpushScripts($isAdmin)
    {
        if (!$isAdmin) {
            if (!$this->shouldShowWebpush()) return; // Prevent showing webpush, does not echo script in the body
        }

        $appId = $this->getSettingValue('app_id');
        $showDialog = $this->getModalOptionsValue('showDialog');
        $showDialog = boolval($showDialog) ? 'true' : 'false';
        $title = $this->getModalOptionsValue('title', 'Subscribe to notifications');
        $content = $this->getModalOptionsValue('content', 'We\'d like to show you notifications for the latest news and updates.');
        $acceptText = $this->getModalOptionsValue('acceptText', 'Allow');
        $rejectText = $this->getModalOptionsValue('rejectText', 'No Thanks');
        $position = $this->getModalOptionsValue('position');
        $icon = $this->getModalOptionsValue('icon');
        $mobilePosition = $this->getModalOptionsValue('mobilePosition');
        $dialogRetryRate = $this->getModalOptionsValue('dialogRetryRate');
        $direction = $this->getModalOptionsValue('dialogDirection', 'ltr');
        $showBell = $this->getBellOptionsValue('is_enabled');
        $showBell = is_bool($showBell) ? $showBell : $showBell === "true" ? true : false;
        $bellBackgroundColor = $this->getBellOptionsValue('backgroundColor');
        $bellColor = $this->getBellOptionsValue('bellColor');

        $output = '';

        if (!$isAdmin) {
            $output .= 'BestPush.init("' . esc_js($appId) . '");';
        }

        $output .= 'var BestPushOptions={';
        $output .= 'showDialog:' . $showDialog;
        if (boolval($title)) {
            $output .= ',title:' . '"' . esc_js($title) . '"';
        }
        if (boolval($content)) {
            $output .= ',content:' . '"' . esc_js($content) . '"';
        }
        if (boolval($acceptText)) {
            $output .= ',acceptText:' . '"' . esc_js($acceptText) . '"';
        }
        if (boolval($rejectText)) {
            $output .= ',rejectText:' . '"' . esc_js($rejectText) . '"';
        }
        if (boolval($position)) {
            $output .= ',position:' . '"' . esc_js($position) . '"';
        }
        if (boolval($mobilePosition)) {
            $output .= ',mobilePosition:' . '"' . esc_js($mobilePosition) . '"';
        }
        if (boolval($dialogRetryRate)) {
            $output .= ',dialogRetryRate:' . esc_js($dialogRetryRate);
        }
        if (boolval($icon)) {
            $output .= ',icon:"' . esc_js($icon) . '"';
        }
        if (boolval($direction)) {
            $output .= ',direction:' . '"' . esc_js($direction) . '"';
        }

        # bell
        if (boolval($showBell)) {
            $output .= ', showBell:true, bell:{ properties: { show: true'; // show: true is only here to prevent begining `,` to cause error

            if (boolval($bellBackgroundColor)) {
                $output .= ',backgroundColor:' . '"' . esc_js($bellBackgroundColor) . '"';
            }

            if (boolval($bellColor)) {
                $output .= ',bellColor:' . '"' . esc_js($bellColor) . '"';
            }

            $output .= '}}';
        }


        $output .= '};';

        if (!$isAdmin) {
            $output .= 'BestPush.subscribe(BestPushOptions);';
        }

        if ($this->webpush_enabled || $isAdmin) {
            wp_enqueue_script('best_push_initialize_scripts', 'https://static.bestpush.io/bestpushweb.js', array(), null, true);
            wp_add_inline_script('best_push_initialize_scripts', $output);
        }
    }

    public function getSettingValue($key)
    {
        if (isset($this->best_push_settings)) {
            return isset($this->best_push_settings[$key]) ? $this->best_push_settings[$key] : null;
        }
    }

    public function getModalOptionsValue($key, $default = null)
    {
        if (isset($this->best_push_modal_options) && array_key_exists($key, $this->best_push_modal_options) && $this->best_push_modal_options[$key]) {
            return $this->best_push_modal_options[$key];
        }
        return $default;
    }

    public function getBellOptionsValue($key, $default = null)
    {
        if (isset($this->best_push_bell_options) && array_key_exists($key, $this->best_push_bell_options) && $this->best_push_bell_options[$key]) {
            return $this->best_push_bell_options[$key];
        }
        return $default;
    }
}
