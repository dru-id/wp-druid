<?php namespace WP_Druid\Admin\Controllers;

use WP_Druid\Admin\Services\Admin_Messages;

/**
 * @package WP Druid
 * @subpackage Controllers
 */
class Admin_Controller
{
	protected $sections = array();

	protected $data = array();

	public function __construct()
    {
        global $pagenow;
        $this->data['current_admin_page'] =  add_query_arg( 'page', WP_DRUID, admin_url( $pagenow ) );


        if (isset($_REQUEST['druid_admin_add_notice'])) {
            if($_REQUEST['druid_admin_add_notice'] === "success") {
                Admin_Messages::success(htmlspecialchars( print_r( $_REQUEST['druid_response'], true)));
            } else {
                Admin_Messages::error(htmlspecialchars( print_r( $_REQUEST['druid_response'], true)));
            }
        }
    }

}
