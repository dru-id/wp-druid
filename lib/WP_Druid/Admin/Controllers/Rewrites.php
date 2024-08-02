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
class Rewrites extends Admin_Controller
{
    public function __construct()
    {
		parent::__construct();
	}

	public function index()
    {
        druid_x(new \WP_Druid\Front\Router())->add_rewrite_rules();
        flush_rewrite_rules();

		Render_Service::render('admin/pages/rewrites', $this->data);
	}


}
