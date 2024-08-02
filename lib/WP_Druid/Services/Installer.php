<?php namespace WP_Druid\Services;

use WP_Druid\Front\Router;
use WP_Druid\Services\DB as DB_Service;

class Installer
{
    public function install() {
        register_activation_hook(WPDR_PLUGIN_FILE, function(){
            druid_x(new DB_Service())->install_db();

            druid_x(new Router())->add_rewrite_rules();
            flush_rewrite_rules();

            add_option('druid_plugin_version', WPDR_VERSION);
        });
    }

    public function installed() {
        register_deactivation_hook(WPDR_PLUGIN_FILE, function(){
            druid_x(new DB_Service())->clean_db();

            druid_x(new Router())->remove_rewrite_rules();
            flush_rewrite_rules();

            delete_option('druid_plugin_version');
        });

        // If user logged is a druid user/subscriber disable admin
        add_action('admin_init', function() {
            if (current_user_can('subscriber') && !defined('DOING_AJAX')) {
                exit(wp_redirect(home_url( '/' )));
            }
        });
    }

    public function loaded() {
        add_action('plugins_loaded', function(){
            druid_x(new DB_Service())->initialize_wpdb_tables();
        }, 10, 0);

        add_action('after_setup_theme', function() {
            if (!current_user_can('administrator')) {
                show_admin_bar(false);
            }
            load_child_theme_textdomain(WPDR_LANG_NS, WPDR_PLUGIN_DIR.'languages');
        });

    }

}