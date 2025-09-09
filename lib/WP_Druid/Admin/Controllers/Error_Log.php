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

        if (empty($wpdb->druid_error_logs)) {
            (new \WP_Druid\Services\DB())->initialize_wpdb_tables();
        }

        $table = $wpdb->druid_error_logs;

        $sql = 'SELECT *
                FROM ' . $table . '
                WHERE logged_at > %s
                ORDER BY logged_at DESC';

        // Último mes en formato DATETIME válido, TZ de WP
        $since = date('Y-m-d H:i:s', strtotime('-1 month', current_time('timestamp')));

        $data['data'] = $wpdb->get_results($wpdb->prepare($sql, $since));

        if (is_null($data['data']) || $data['data'] instanceof \WP_Error) {
            return null;
        }

        Render_Service::render('admin/pages/home-error-log', $data);
    }
}
