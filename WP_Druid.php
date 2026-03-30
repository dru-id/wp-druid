<?php defined('ABSPATH') or die('Direct access to this file is not allowed.');
/**
 * Plugin Name: Druid for WordPress
 * Description: Implements the Druid solution into WordPress
 * Version: 1.0.0
 * Requires at least: 6.0
 * Tested up to: 6.9.1
 * Requires PHP: 7.2.24
 * Author: Genetsis
 * Author URI: https://druid.com
 */

require_once __DIR__ . '/vendor/autoload.php';

define('WP_DRUID', 'druid'); // Use this key to check if this plugin exists.
define('WPDR_PLUGIN_PUBLIC_NAME', 'DRUID for WordPress');
define('WPDR_PLUGIN_NAME', plugin_basename(__FILE__));
define('WPDR_PLUGIN_FILE', __FILE__);
define('WPDR_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WPDR_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('DRUID_DB_VERSION', 2);
define('WPDR_VERSION', '1.0.0');
define('WPDR_LANG_NS', 'WP_Druid');

define('WPDR_PREVIOUS_URL_SESSION_KEY', 'previous_url');
define('WPDR_REFERER_URL_SESSION_KEY', 'referer_url');
define('WPDR_CURRENT_LOCALE', '');
define('WPDR_USER_CANCEL_ACTION_SESSION_KEY', 'user_cancel_action');
define('WPDR_CUSTOM_RETURN_URL_SESSION_KEY', 'custom_return_url');

register_activation_hook(WPDR_PLUGIN_FILE, function () {
    try {
        druid_x(new \WP_Druid\Services\DB())->install_db();

        druid_x(new \WP_Druid\Front\Router())->add_rewrite_rules();
        flush_rewrite_rules();

        update_option('druid_plugin_version', WPDR_VERSION);
    } catch (\Throwable $e) {
        error_log('WP_Druid activation failed: ' . $e->getMessage());
        deactivate_plugins(WPDR_PLUGIN_NAME);
        wp_die(esc_html__('DruID could not be activated. Check the PHP error log for details.', WPDR_LANG_NS), esc_html__('Error', WPDR_LANG_NS), array('response' => 500));
    }
});

register_deactivation_hook(WPDR_PLUGIN_FILE, function () {
    try {
        druid_x(new \WP_Druid\Front\Router())->remove_rewrite_rules();
        flush_rewrite_rules();
    } catch (\Throwable $e) {
        error_log('WP_Druid deactivation failed: ' . $e->getMessage());
    }
});

add_action('plugins_loaded', function () {
    try {
        $db = new \WP_Druid\Services\DB();
        $db->initialize_wpdb_tables();
        $db->check_update();
    } catch (\Throwable $e) {
        error_log('WP_Druid database bootstrap failed: ' . $e->getMessage());
    }
}, 1);

/**
 * Plugin main class.
 */
class WP_Druid
{
    public function init()
    {
        add_action('init', array($this, 'load_textdomain'));

        \WP_Druid\Utils\Session\Services\SessionManager::ensure_started();

        try {
            if (is_admin()) {
                druid_x(new \WP_Druid\Admin\WP_Druid_Admin())->init();
            } else {
                druid_x(new \WP_Druid\Front\WP_Druid_Public())->init();
            }
        } catch (\Throwable $e) {
            \WP_Druid\Services\Errors::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e);
        }

        $this->setup_shortcodes();
    }

    public function load_textdomain()
    {
        load_plugin_textdomain(WPDR_LANG_NS, false, dirname(WPDR_PLUGIN_NAME) . '/languages');
    }

    /**
     * Register shortcodes.
     */
    private function setup_shortcodes()
    {
        add_shortcode('druid_auth_controls', array('\WP_Druid\Services\Shortcodes', 'get_druid_auth_controls'));
        add_shortcode('druid_auth_controls_login', array('\WP_Druid\Services\Shortcodes', 'get_druid_auth_controls_login'));
        add_shortcode('druid_auth_controls_register', array('\WP_Druid\Services\Shortcodes', 'get_druid_auth_controls_register'));
        add_shortcode('druid_auth_controls_edit_account', array('\WP_Druid\Services\Shortcodes', 'get_druid_auth_controls_edit_account'));
        add_shortcode('druid_auth_controls_logout', array('\WP_Druid\Services\Shortcodes', 'get_druid_auth_controls_logout'));
    }
}

druid_x(new WP_Druid())->init();
