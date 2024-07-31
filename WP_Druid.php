<?php defined('ABSPATH') or die('Direct access to this file is not allowed.');
/**
 * Plugin Name: DruID for WordPress
 * Description: Implements the DruID ciam integration into Wordpress
 * Version: 1.0.0
 * Author: DruID
 * Author URI: https://dru-id.com
 */

require_once __DIR__.'/vendor/autoload.php';

use Genetsis\Identity;
use WP_Druid\Services\Router as Router_Service;
use WP_Druid\Services\Callbacks\Logout as Logout_Callback;
use WP_Druid\Services\DB as DB_Service;
use WP_Druid\Utils\Wp\Services\Admin_Messages;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Admin_Menu\Services\Admin_Menu_Manager;
use WP_Druid\Services\Shortcodes;
use WP_Druid\Services\Users as Users_Service;
use WP_Druid\Services\Pulse\PulseRestClient as Pulse_Client;

define('WP_DRUID', 'druid'); // Use this key to check if this plugin exists.
define('WPDR_PLUGIN_PUBLIC_NAME', 'DruID for Wordpress');
define('WPDR_PLUGIN_NAME', plugin_basename(__FILE__));
define('WPDR_PLUGIN_FILE', __FILE__);
define('WPDR_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WPDR_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('DRUID_DB_VERSION', 1);
define('WPDR_VERSION', '1.0.0');
define('WPDR_LANG_NS', 'WP_Druid');

define('WPDR_PREVIOUS_URL_SESSION_KEY', 'previous_url');
define('WPDR_REFERER_URL_SESSION_KEY', 'referer_url');
define('WPDR_CURRENT_LANGUAGE_CODE_SESSION_KEY', 'current_language');
define('WPDR_PREVIOUS_LANGUAGE_CODE_SESSION_KEY', 'previous_language');
define('WPDR_USER_CANCEL_ACTION_SESSION_KEY', 'user_cancel_action');
define('WPDR_CUSTOM_RETURN_URL_SESSION_KEY', 'custom_return_url');

/**
 * Plugin main class.
 *
 * @package WP_Druid
 */
class WP_Druid
{
    public function init()
    {
        if (version_compare(phpversion(), '5.3', '<')) {
            if (is_admin()) {
                Admin_Messages::error('<strong>' . WPDR_PLUGIN_PUBLIC_NAME . ':</strong> You must have at least version 5.3 of PHP to use this plugin.');
            }
            return false;
        }
        // TODO: add here all server requirements (PHP version, components, ...)

        try {
            if (session_id() === '') {
                session_start();
            }

            // Stores current and previous language code.
            WP_Druid\Utils\Session\Services\SessionManager::set(WPDR_PREVIOUS_LANGUAGE_CODE_SESSION_KEY, WP_Druid\Utils\Session\Services\SessionManager::has(WPDR_CURRENT_LANGUAGE_CODE_SESSION_KEY)
                ? WP_Druid\Utils\Session\Services\SessionManager::get(WPDR_CURRENT_LANGUAGE_CODE_SESSION_KEY)
                : get_locale());
            WP_Druid\Utils\Session\Services\SessionManager::set(WPDR_CURRENT_LANGUAGE_CODE_SESSION_KEY, get_locale());

            // TODO: temporal solution with the problem of two or more different clients IDs under the same domain.
            // Every the user changes the language of the page, which changes the DruID client, we have to remove the
            // cookies related to the previous client and remove the stored "Things" object from the session handler.
            // Dirty, yes, but it works.
            if ((WP_Druid\Utils\Session\Services\SessionManager::get(WPDR_PREVIOUS_LANGUAGE_CODE_SESSION_KEY) != WP_Druid\Utils\Session\Services\SessionManager::get(WPDR_CURRENT_LANGUAGE_CODE_SESSION_KEY)) && isset($_SESSION['Things'])) {
                unset($_SESSION['Things']);
                if (isset($_COOKIE)) {
                    $cookies = array(\Genetsis\core\iTokenTypes::ACCESS_TOKEN, \Genetsis\core\iTokenTypes::CLIENT_TOKEN, \Genetsis\core\iTokenTypes::REFRESH_TOKEN);
                    foreach ($cookies as $c) {
                        if (isset($_COOKIE[$c])) {
                            unset($_COOKIE[$c]);
                        }
                    }
                }
            }

            Identity::init(druid_get_current_client(), (is_admin() ? false : true));

        } catch (Exception $e) {

            if (is_admin()) {
                Admin_Messages::error($e->getMessage());
            }
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);

        }

        $this->setup_actions();
        $this->setup_filters();
        $this->setup_shortcodes();

        if (is_admin()) {
            druid_x(new Admin_Menu_Manager())->init();
        }
    }

    /**
     * In this method we will set up all action for this plugin.
     *
     * @return void
     */
    private function setup_actions()
    {
        add_action('init', function(){
            druid_x(new Router_Service())->init();

            if (!is_admin()) {
                // Forces user to logout if is not logged in Wordpress but logged in DruID.
                if (!is_user_logged_in() && Identity::isConnected()) {
                    Identity::logoutUser();
                }

                // TODO: Forces user to logout if is not logged in DruID but logged in Wordpress.

                Identity::isConnected()
                    ? do_action(WPDR_ACTION_USER_IS_LOGGED)
                    : do_action(WPDR_ACTION_USER_IS_NOT_LOGGED);
            }

        }, 10, 0);

        add_action('wp_enqueue_scripts', function(){
            wp_enqueue_script('wpdr-login-sso', 'https://login.ciam.demo.dru-id.com/login/sso');
        });

        add_action('plugins_loaded', function(){ druid_x(new DB_Service())->initialize_wpdb_tables(); }, 10, 0);

        add_action('wp_logout', function(){ druid_x(new Logout_Callback())->run(); }, 10, 0);

        add_action('after_setup_theme', function(){
            load_child_theme_textdomain(WPDR_LANG_NS, WPDR_PLUGIN_DIR.'languages');
        });

        if (is_admin()) { // Explicit actions for admin.
            register_activation_hook(WPDR_PLUGIN_FILE, function(){
                druid_x(new DB_Service())->install_db();
                flush_rewrite_rules();
            });
            register_deactivation_hook(WPDR_PLUGIN_FILE, function(){
                druid_x(new DB_Service())->clean_db();
                flush_rewrite_rules();
            });
            add_action('activated_plugin', function ($plugin) {
                druid_x(new Router_Service())->add_rewrite_rules();
                flush_rewrite_rules();
                if ($plugin == WPDR_PLUGIN_NAME) {
                    exit(wp_redirect(admin_url('admin.php?page=wpdr')));
                }
            });
            add_action('deactivated_plugin', function($plugin) {
                druid_x(new Router_Service())->add_rewrite_rules();
                flush_rewrite_rules();
            });
            add_action('plugins_loaded', function(){ druid_x(new DB_Service())->check_update(); });
            add_action('deleted_user', function($wp_user_id) { Users_Service::delete_local_druid_user($wp_user_id); } );
            add_action('wp_ajax_send_click', function() {
                if (isset($_POST['id']) && isset($_POST['url']) && isset($_POST['current_url']) && isset($_POST['text'])) {
                    $id = $_POST['id'];
                    $url = $_POST['url'];
                    $current_url = $_POST['current_url'];
                    $text = $_POST['text'];
                    $objectId = Identity::getThings()->getLoginStatus()->getOid();
                    Pulse_Client::send_event($objectId, 'click', $id, $current_url, $url, $text);
                } else {
                    error_log('Error: Los parámetros URL y Texto son obligatorios.');
                }
            });
        } else { // Explicit actions for non-admin.
            add_action('template_redirect', function() {
                if (!is_admin() && is_user_logged_in() && Identity::isConnected()) {
                    $objectId = Identity::getThings()->getLoginStatus()->getOid();
                    Pulse_Client::send_event($objectId, 'view');
                }
            }, 0, 10);
        }

    }

    /**
     * In this method we will set up all filters for this plugin.
     *
     * @return void
     */
    private function setup_filters()
    {
        if (is_admin()) { // Filters to be executed only in admin pages.
            // Add settings links.
            add_filter('plugin_action_links_'.WPDR_PLUGIN_NAME, function($links) {
                $settings_link = '<a href="admin.php?page=wpdr-errors">' . __('Error Log', WPDR_LANG_NS) . '</a>';
                array_unshift($links, $settings_link);

                $settings_link = '<a href="admin.php?page=wpdr">' . __('Settings', WPDR_LANG_NS) . '</a>';
                array_unshift($links, $settings_link);

                return $links;
            });
        }
    }

    /**
     * In this method we will set up all shortcodes for this plugin.
     *
     * @return void
     */
    private function setup_shortcodes()
    {
        add_shortcode(DRUID_AUTH_CONTROLS, array('\WP_Druid\Services\Shortcodes', 'get_druid_auth_controls'));
        add_shortcode(CUSTOM_LINK, array('\WP_Druid\Services\Shortcodes', 'get_custom_link'));
    }
}

druid_x(new WP_Druid())->init();
