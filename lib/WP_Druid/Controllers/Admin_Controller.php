<?php namespace WP_Druid\Controllers;

/**
 * @package WP Druid
 * @subpackage Controllers
 */
class Admin_Controller
{
	protected $sections = array();

	public function __construct()
    {
        wp_enqueue_media();
        wp_enqueue_style( 'wpdr_bootstrap', WPDR_PLUGIN_URL . 'assets/bootstrap/css/bootstrap.min.css' );
        wp_enqueue_script( 'wpdr_bootstrap', WPDR_PLUGIN_URL . 'assets/bootstrap/js/bootstrap.min.js' );
	}
}
