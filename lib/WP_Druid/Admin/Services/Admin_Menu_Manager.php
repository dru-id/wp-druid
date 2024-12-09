<?php namespace WP_Druid\Admin\Services;

use WP_Druid\Admin\Controllers\Error_Log;
use WP_Druid\Admin\Controllers\Home;

/**
 * @package WP Druid
 */
class Admin_Menu_Manager
{
    protected $error_log;
    protected $druid_admin;
    protected $import_settings;

    public function __construct()
    {
    }

    public function init()
    {
        add_action('admin_menu', function(){
            $main_menu = WP_DRUID;

            // Home page.
            add_menu_page(__('DruID', WPDR_LANG_NS), __('DruID', WPDR_LANG_NS), 'manage_options', $main_menu, function(){ druid_x(new Home())->index(); }, WPDR_PLUGIN_URL.'/assets/img/menu_logo.png', 80);
            // Error logs.
            add_submenu_page($main_menu, __('Error Log', WPDR_LANG_NS), __('Error Log', WPDR_LANG_NS), 'manage_options', 'druid-errors', function(){ druid_x(new Error_Log())->index(); }, 80.5, 0 );

            });

        do_action('acf/input/admin_head');
        do_action('acf/input/admin_enqueue_scripts');
    }
}
