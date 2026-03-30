<?php namespace WP_Druid\Admin\Controllers;

use WP_Druid\Admin\Router;
use WP_Druid\DAO\ConfigDAO;
use WP_Druid\Models\Config;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Services\Render as Render_Service;
use WP_Druid\Utils\Wp\Services\Query_Vars as Query_Vars_Service;

/**
 * @package WP Druid
 * @subpackage Controllers
 */
class Home extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->data['tab'] = Query_Vars_Service::find('tab', 'config');

        $dao = new ConfigDAO();
        $actual_config = $dao->get();

        $this->data['actual_config'] = $actual_config;

        Render_Service::render('admin/pages/home-settings', $this->data);
    }

    public function post() {
        if (!current_user_can('manage_options')) {
            wp_die(
                esc_html__('You are not allowed to manage DruID settings.', WPDR_LANG_NS),
                esc_html__('Error', WPDR_LANG_NS),
                array('response' => 403)
            );
        }

        if( isset( $_POST['druid_meta_nonce'] ) && wp_verify_nonce( wp_unslash($_POST['druid_meta_nonce']), 'druid_edit_config') ) {
            try {
                $post_data = wp_unslash($_POST);

                // sanitize the input
                $config = new Config();
                $config->setClientId(sanitize_text_field($post_data['client_id'] ?? ''))
                    ->setClientSecret(sanitize_text_field($post_data['client_secret'] ?? ''))
                    ->setEntryPoint(sanitize_text_field($post_data['entry_points'] ?? ''))
                    ->setLogLevel(sanitize_text_field($post_data['log_level'] ?? ''))
                    ->setLogPath(sanitize_text_field($post_data['log_path'] ?? ''))
                    ->setCachePath(sanitize_text_field($post_data['cache_path'] ?? ''))
                    ->setEnvironment(sanitize_text_field($post_data['environment'] ?? ''))
                    ->setCallback(sanitize_text_field($post_data['callback'] ?? ''))
                    ->setDomain(sanitize_text_field($post_data['domain'] ?? ''));

                $dao = new ConfigDAO();
                $dao->update($config);

            } catch (\Throwable $e) {
                Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e);
                Router::custom_redirect(WP_DRUID, "error", $e->getMessage());
                exit;
            }

            // redirect the user to the appropriate page
            Router::custom_redirect(WP_DRUID, "success", __('Configuration Saved!', WPDR_LANG_NS));
            exit;
        } else {
            wp_die( __( 'Invalid nonce specified', WPDR_LANG_NS ), __( 'Error', WPDR_LANG_NS), array(
                'response' 	=> 403,
                'back_link' => 'admin.php?page=' . WP_DRUID,
            ) );
        }

    }

}
