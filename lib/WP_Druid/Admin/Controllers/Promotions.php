<?php namespace WP_Druid\Admin\Controllers;


use WP_Druid\Admin\Services\Admin_Messages;
use WP_Druid\Admin\Services\Promotion_List;
use WP_Druid\Services\Render as Render_Service;
use WP_Druid\Utils\Wp\Services\Query_Vars as Query_Vars_Service;

/**
 * @package WP Druid
 * @subpackage Controllers
 */
class Promotions extends Admin_Controller
{
    public function __construct()
    {
		parent::__construct();
	}

	public function index()
    {
        if (!empty($_POST)) {

            if( isset($_POST['acf']) ) {
                acf_validate_values( $_POST['acf'], 'acf' );

                //acf_validate_save_post(false);
                //var_dump(acf_get_validation_errors());
                if (!empty(acf_get_validation_errors())) {
                    foreach (acf_get_validation_errors() as $error) {
                        //acf_add_validation_error($error['input'], $error['message']);
                        Admin_Messages::error($error['message']);
                    }
                }

                $fields = $_POST['acf'];
                foreach ($fields as $field => $value) {
                    $valid_field = acf_get_field($field);
                    //var_dump($valid_field);
                    var_dump($value);
                    echo '<br>';
                }

            }
        }

        $this->data['tab'] = Query_Vars_Service::find('tab', 'promotions');

        $this->data['promotions_list'] = new Promotion_List();

		Render_Service::render('admin/pages/home-promotions', $this->data);
	}

}
