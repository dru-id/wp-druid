<?php defined('ABSPATH') or die('Direct access to this file is not allowed.');
/**
 * Plugin Name: Druid for WordPress
 * Description: Implements the Druid solution into Wordpress
 * Version: 1.0.0
 * Author: Genetsis
 * Author URI: https://druid.com
 */

require_once __DIR__.'/vendor/autoload.php';

define('WP_DRUID', 'druid'); // Use this key to check if this plugin exists.
define('WPDR_PLUGIN_PUBLIC_NAME', 'DRUID for Wordpress');
define('WPDR_PLUGIN_NAME', plugin_basename(__FILE__));
define('WPDR_PLUGIN_FILE', __FILE__);
define('WPDR_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('WPDR_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
define('DRUID_DB_VERSION', 1);
define('WPDR_VERSION', '1.0.0');
define('WPDR_LANG_NS', 'WP_Druid');

define('WPDR_PREVIOUS_URL_SESSION_KEY', 'previous_url');
define('WPDR_REFERER_URL_SESSION_KEY', 'referer_url');
define('WPDR_CURRENT_LOCALE', '');
define('WPDR_USER_CANCEL_ACTION_SESSION_KEY', 'user_cancel_action');
define('WPDR_CUSTOM_RETURN_URL_SESSION_KEY', 'custom_return_url');

/**
 * Plugin main class.
 *
 * @package WP_Druid
 */
class WP_Druid
{

	public function init() {
        if (session_id() === '') {
            session_start();
        }

        if (is_admin()) {
            druid_x(new \WP_Druid\Admin\WP_Druid_Admin())->init();
        } else {
            druid_x(new \WP_Druid\Front\WP_Druid_Public())->init();
        }

        $this->setup_shortcodes();

	}


    /**
     * In this method we will set up all shortcodes for this plugin.
     *
     * @return void
     */
    private function setup_shortcodes()
    {
        add_shortcode('druid_auth_controls', array('\WP_Druid\Services\Shortcodes', 'get_druid_auth_controls'));
    }
}

druid_x(new WP_Druid())->init();
