<?php namespace WP_Druid\Admin\Controllers;

use Genetsis\core\OAuthConfig;
use WP_Druid\Admin\Router;
use WP_Druid\DAO\ConfigDAO;
use WP_Druid\Models\Config;
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

        if( isset( $_POST['druid_meta_nonce'] ) && wp_verify_nonce( $_POST['druid_meta_nonce'], 'druid_edit_config') ) {
            try {
                // sanitize the input
                $config = new Config();
                $config->setClientId(sanitize_text_field($_POST['client_id']))
                    ->setClientSecret(sanitize_text_field($_POST['client_secret']))
                    ->setEntryPoint(sanitize_text_field($_POST['entry_points']))
                    ->setLogLevel(sanitize_text_field($_POST['log_level']))
                    ->setLogPath(sanitize_text_field($_POST['log_path']))
                    ->setCachePath(sanitize_text_field($_POST['cache_path']))
                    ->setEnvironment(sanitize_text_field($_POST['environment']))
                    ->setCallback(sanitize_text_field($_POST['callback']))
                    ->setDomain(sanitize_text_field($_POST['domain']));

                $dao = new ConfigDAO();
                $dao->update($config);

            } catch (\Exception $e) {
                Router::custom_redirect(WP_DRUID, "error", $e->getMessage());
                exit;
            }

            // redirect the user to the appropriate page
            Router::custom_redirect(WP_DRUID, "success", "Configuration Saved!");
            exit;
        } else {
            wp_die( __( 'Invalid nonce specified', WP_DRUID ), __( 'Error', WP_DRUID), array(
                'response' 	=> 403,
                'back_link' => 'admin.php?page=' . WP_DRUID,
            ) );
        }

    }

}
