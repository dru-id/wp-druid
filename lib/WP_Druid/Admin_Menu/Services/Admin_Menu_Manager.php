<?php namespace WP_Druid\Admin_Menu\Services;

use WP_Druid\Controllers\Admin\Home;
use WP_Druid\Controllers\Admin\Error_Log;

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
                $main_menu = 'wpdr';

                // Home page.
                // TODO: add icon (WPDR_PLUGIN_URL . 'assets/img/menu-icon.png')
                add_menu_page(__('DruID', WPDR_LANG_NS), __('DruID', WPDR_LANG_NS), 'manage_options', $main_menu, function(){ druid_x(new Home())->index(); }, '', 80);
                // Error logs.
                add_submenu_page($main_menu, __('Error Log', WPDR_LANG_NS), __('Error Log', WPDR_LANG_NS), 'manage_options', 'wpdr-errors', function(){ druid_x(new Error_Log())->index(); }, 80, 0 );
            });
    }
}
