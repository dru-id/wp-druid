<?php namespace WP_Druid\Services;

use Genetsis\Identity;
use Genetsis\UserApi;
use Genetsis\URLBuilder;
use WP_Druid\Services\Render as Render_Service;
use WP_Druid\Services\Errors as Errors_Service;

/**
 * @package WP Druid
 */
class Shortcodes
{
    /**
     * Returns the DruId login controls.
     *
     * User's hooks can be attached to {@link DRUID_AUTH_CONTROLS} to render custom auth controls. These hooks will receive
     * an array with the following data:
     *  - is_user_logged (boolean) TRUE if the user is logged, FALSE if not. To show the proper controls.
     *  - name (string) User's name. It could be empty.
     *  - surname (string) User's surname. It could be empty.
     *  - email (string) User's email. It could be empty.
     *  - login_url (string) Optional
     *  - register_url
     *  - edit_account_url
     *  - logout_url
     *
     * @param array $attributes If defined accepts:
     *      - entry-point: Entry point identifier.
     * @return string
     */
    public static function get_druid_auth_controls ($attributes = array())
    {
        ob_start();

        try{

            $scope = (isset($attributes['entry-point']) && $attributes['entry-point'])
                ? $attributes['entry-point']
                : null;
            $data = array(
                'login_url' => URLBuilder::getUrlLogin($scope),
                'register_url' => URLBuilder::getUrlRegister($scope),
                'edit_account_url' => URLBuilder::getUrlEditAccount($scope),
                'logout_url' => '/actions/logout',
            );
            if (Identity::isConnected()) {
                $data['is_user_logged'] = true;
                $info = UserApi::getUserLogged();
                //¡¡var_dump($info);
                if (!is_null($info)) {
                    $data['name'] = (isset($info->user->user_data->name->value) && $info->user->user_data->name->value) ? $info->user->user_data->name->value : '';
                    $data['surname'] = (isset($info->user->user_data->surname->value) && $info->user->user_data->surname->value) ? $info->user->user_data->surname->value : '';
                    $data['email'] = isset($info->user->user_ids->email->value) ? $info->user->user_ids->email->value : '';
                }
            } else {
                $data['is_user_logged'] = false;
                if (isset($attributes['social']) && $attributes['social']) {
                    $data['login_url'] = URLBuilder::getUrlLogin($scope, $attributes['social']);
                }
            }

            // If there is more than one hooks attached to this action then we deletegate render to these hooks,
            // else we should generate a basic control layer.
            (druid_count_hooked_functions(DRUID_AUTH_CONTROLS) > 0)
                ? do_action(DRUID_AUTH_CONTROLS, $data)
                : Render_Service::render('public/auth-controls', $data);

        } catch (\Exception $e) {

            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);

        }

        return ob_get_clean();
    }

    public static function get_druid_auth_controls2 ($attributes = array())
    {
        ob_start();

        try{
            $data_value='';
            if (isset($attributes['data-value'])) $data_value=$attributes['data-value'];
            $scope = (isset($attributes['entry-point']) && $attributes['entry-point'])
                ? $attributes['entry-point']
                : null;
            $data = array(
                'login_url' => URLBuilder::getUrlLogin($scope),
                'register_url' => URLBuilder::getUrlRegister($scope),
                'edit_account_url' => URLBuilder::getUrlEditAccount($scope),
                'logout_url' => '/actions/logout',
                'data-value' => $data_value,
            );
            if (Identity::isConnected()) {
                $data['is_user_logged'] = true;
                $info = UserApi::getUserLogged();
                if (!is_null($info)) {
                    $data['oid'] = $info->user->oid;
                    $data['name'] = (isset($info->user->user_data->name->value) && $info->user->user_data->name->value) ? $info->user->user_data->name->value : '';
                    $data['surname'] = (isset($info->user->user_data->surname->value) && $info->user->user_data->surname->value) ? $info->user->user_data->surname->value : '';
                    $data['email'] = isset($info->user->user_ids->email->value) ? $info->user->user_ids->email->value : '';
                }
                if (!Identity::checkUserComplete($scope)){
                    //$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
                    $protocol = isset($_SERVER['HTTPS'])===true ? 'https://' : 'http://';
                    $state=$protocol.$_SERVER['SERVER_NAME']."/wog-promo-gracias/";
                    $data['urlCompleteAccount'] = URLBuilder::getUrlCompleteAccount($scope, null, $state);
                }
            } else {
                $data['is_user_logged'] = false;
                if (isset($attributes['social']) && $attributes['social']) {
                    $data['login_url'] = URLBuilder::getUrlLogin($scope, $attributes['social']);
                }
            }

            // If there is more than one hooks attached to this action then we deletegate render to these hooks,
            // else we should generate a basic control layer.
            (druid_count_hooked_functions(DRUID_AUTH_CONTROLS2) > 0)
                ? do_action(DRUID_AUTH_CONTROLS2, $data)
                : Render_Service::render('public/auth-controls2', $data);

        } catch (\Exception $e) {

            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);

        }

        return ob_get_clean();
    }

}