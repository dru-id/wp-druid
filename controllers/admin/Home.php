<?php namespace WP_Druid\Controllers\Admin;

use Genetsis\core\OAuthConfig;
use WP_Druid\Controllers\Admin_Controller;
use WP_Druid\Services\Render as Render_Service;

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
        $data = array();
        $base_url = OAuthConfig::getApiUrl('api.user', 'base_url');
        $data['client_data'] = array(
            'client_id' => OAuthConfig::getClientId(),
            'redirects' => array(
                'confirm-user' => OAuthConfig::getRedirectUrl('confirmUser'),
                'post-edit-account' => OAuthConfig::getRedirectUrl('postEditAccount'),
                'post-change-email' => OAuthConfig::getRedirectUrl('postChangeEmail'),
                'register' => OAuthConfig::getRedirectUrl('register'),
                'post-login' => OAuthConfig::getRedirectUrl('postLogin'),
            ),
            'apis' => array(
                $base_url . OAuthConfig::getApiUrl('api.user', 'user')
            )
        );
		Render_Service::render('admin/pages/settings/home-settings', $data);
	}
}
