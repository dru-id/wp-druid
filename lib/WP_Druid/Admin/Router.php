<?php namespace WP_Druid\Admin;

use WP_Druid\Admin\Controllers\Home;

/**
 * Here is where you will register all of the routes accepted by this plugin.
 *
 * @package WP Druid
 */
class Router
{

    public function init()
    {
        // Add settings links in plugin page
        add_filter('plugin_action_links_'.WPDR_PLUGIN_NAME, function($links) {
            $settings_link = '<a href="admin.php?page=druid">' . __('Settings', WPDR_LANG_NS) . '</a>';
            array_unshift($links, $settings_link);
            return $links;
        });

        // POST handlers action='edit_druid_settings'
        add_action( 'admin_post_edit_druid_settings', function(){ druid_x(new Home())->post(); });
    }


    /**
     * Redirect
     *
     * @since    1.0.0
     */
    public static function custom_redirect( $page, $admin_notice, $response ) {

        wp_redirect( esc_url_raw( add_query_arg( array(
                'druid_admin_add_notice' => $admin_notice,
                'druid_response' => $response,
            ),
            admin_url('admin.php?page='. $page)
        )));
    }

}