<?php namespace WP_Druid\Admin;

use WP_Druid\Admin\Services\Admin_Menu_Manager;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Services\Users as Users_Service;

class WP_Druid_Admin
{
    public function init()
    {
        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        add_action('admin_init', function () {
            druid_x(new Router())->init();
            if (current_user_can('subscriber') && !defined('DOING_AJAX')) {
                wp_safe_redirect(home_url('/'));
                exit;
            }
        });

        druid_x(new Admin_Menu_Manager())->init();
        wp_enqueue_script('wp-ajax-response');
        $this->enque_media();

        add_action('deleted_user', function ($wp_user_id) {
            Users_Service::delete_local_druid_user($wp_user_id);
        });

        IdentityFactory::init();
    }

    private function enque_media()
    {
        wp_enqueue_script('wp-ajax-response');

        add_action('wp_enqueue_scripts', function () {
            //wp_enqueue_media();
            //wp_enqueue_style( 'wpdr_bootstrap', WPDR_PLUGIN_URL . 'assets/bootstrap/css/bootstrap.min.css' );
            //wp_enqueue_script( 'wpdr_bootstrap', WPDR_PLUGIN_URL . 'assets/bootstrap/js/bootstrap.min.js' );
        });
    }
}
