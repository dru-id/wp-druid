<?php namespace WP_Druid\Admin;

use WP_Druid\Admin\Services\Admin_Menu_Manager;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Services\Users as Users_Service;

class WP_Druid_Admin
{
    public function init()
    {
        if (!class_exists('WP_List_Table')) {
            require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
        }

        add_action('admin_init', function () {
            try {
                druid_x(new Router())->init();
                if (current_user_can('subscriber') && !defined('DOING_AJAX')) {
                    wp_safe_redirect(home_url('/'));
                    exit;
                }
            } catch (\Throwable $e) {
                Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e);
            }
        });

        druid_x(new Admin_Menu_Manager())->init();
        $this->enque_media();

        add_action('deleted_user', function ($wp_user_id) {
            try {
                Users_Service::delete_local_druid_user($wp_user_id);
            } catch (\Throwable $e) {
                Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e);
            }
        });

        try {
            IdentityFactory::init();
        } catch (\Throwable $e) {
            Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e);
        }
    }

    private function enque_media()
    {
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('wp-ajax-response');
        });
    }
}
