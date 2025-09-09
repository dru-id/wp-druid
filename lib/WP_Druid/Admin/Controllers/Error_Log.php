<?php namespace WP_Druid\Admin\Controllers;

use WP_Druid\Services\Render as Render_Service;

/**
 * @package WP Druid
 * @subpackage Controllers
 */
class Error_Log extends Admin_Controller
{
	public function __construct()
    {
		parent::__construct();
	}

	public function index()
    {
		global $wpdb;
        $sql = 'SELECT *
        FROM ' . $wpdb->druid_error_logs .'
        WHERE logged_at > %s
        ORDER BY logged_at DESC';

        $data['data'] = $wpdb->get_results(
            $wpdb->prepare($sql, current_time('mysql', 1))
        );

		if ( is_null( $data['data'] ) || $data['data'] instanceof WP_Error ) {
			return null;
		}

        Render_Service::render('admin/pages/home-error-log', $data);
	}

}
