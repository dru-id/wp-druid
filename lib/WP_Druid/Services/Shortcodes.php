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
     * Returns the DruId header controls.
     *
     * User's hooks can be attached to {@link Shortcodes::DRUID_AUTH_CONTROLS} to render custom auth controls. These hooks will receive
     * an array with the following data:
     *  - is_user_logged (boolean) TRUE if the user is logged, FALSE if not. To show the proper controls.
     *  - name (string) User's name. It could be empty.
     *  - surname (string) User's surname. It could be empty.
     *  - email (string) User's email. It could be empty.
     *  - login_url (string) Optional
     *  - register_url
     *  - edit_account_url
     *  - logout_url
     *  - show_login (boolean) If show login link with text. Default true
     *  - show_register (boolean) If show register link with text. Default true
     *  - get_only_url (string) (login|register) only return login or register link, to use in html links. Default null
     *
     * @param array $attributes If defined accepts:
     *      - entrypoint: Entry point identifier.
     * @return string
     */
    public static function get_druid_auth_controls ($attributes = array())
    {
        ob_start();

        try{
            $scope = (isset($attributes['entrypoint']) && $attributes['entrypoint'])
                ? $attributes['entrypoint']
                : null;

            $state = (isset($attributes['state']) && $attributes['state'])
                ? $attributes['state']
                : null;

            $social= (isset($attributes['social']) && $attributes['social']) ? $attributes['social'] : null;

            $locale_param = array('request_locale' => get_locale());

            $data = array(
                'login_url' => URLBuilder::getUrlLogin($scope, $social, null, array(), $state)
                    .'&'.http_build_query($locale_param, '', '&'),
                'register_url' => URLBuilder::getUrlRegister($scope, null, array(), $state)
                    .'&'.http_build_query($locale_param, '', '&')
            );

            $data['show_login'] = (!empty($attributes['show_login'])) ? filter_var($attributes['show_login'],
                FILTER_VALIDATE_BOOLEAN) : true;
            $data['show_register'] = (!empty($attributes['show_register'])) ? filter_var($attributes['show_register'],
                FILTER_VALIDATE_BOOLEAN) : true;
            $data['get_only_url'] = (!empty($attributes['get_only_url'])) ? $attributes['get_only_url']: null;

            if (Identity::isConnected()) {
                $data['is_user_logged'] = true;
                $data['edit_account_url'] = URLBuilder::getUrlEditAccount($scope, null, $state).'&'
                    .http_build_query($locale_param, null, '&');
                $data['logout_url'] = '/druid-actions/logout';

                $info = UserApi::getUserLogged();
                if (!is_null($info)) {
                    $data['name'] = (isset($info->user->user_data->name->value) && $info->user->user_data->name->value) ? $info->user->user_data->name->value : '';
                    $data['surname'] = (isset($info->user->user_data->surname->value) && $info->user->user_data->surname->value) ? $info->user->user_data->surname->value : '';
                    $data['email'] = isset($info->user->user_ids->email->value) ? $info->user->user_ids->email->value : '';
                }
            } else {
                $data['is_user_logged'] = false;
            }

            if ($data['get_only_url'] != null) {
                if ($data['get_only_url'] === 'login') {
                    echo $data['login_url'];
                } else {
                    echo $data['register_url'];
                }
            } else {
                // If there is more than one hooks attached to this action then we deletegate render to these hooks,
                // else we should generate a basic control layer.
                (has_action('druid_auth_controls'))
                    ? do_action('druid_auth_controls', $data)
                    : Render_Service::render('public/auth-controls', $data);
            }


        } catch (\Exception $e) {

            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);

        }

        return ob_get_clean();
    }

    /**
     * Returns the DruId login controls.
     *
     * User's hooks can be attached to {@link Shortcodes::DRUID_AUTH_CONTROLS_LOGIN} to render custom auth controls.
     * @param array $attributes If defined accepts:
     *      - entrypoint: Entrypoint identifier.
     * @return string
     */
    public static function get_druid_auth_controls_login($attributes = array())
    {
        ob_start();

        try {
            $scope = (isset($attributes['entrypoint']) && $attributes['entrypoint'])
                ? $attributes['entrypoint']
                : null;

            $state_attr = (isset($attributes['state']) && $attributes['state'])
                ? $attributes['state']
                : null;
            $pageToRedirect = (isset($attributes['pageToRedirect']) && $attributes['pageToRedirect'])
                ? $attributes['pageToRedirect']
                : null;
            $data = array(
                'pageToRedirect' => $pageToRedirect,
                'state' => $state_attr
            );
            $json_data = json_encode($data);
            $state = base64_encode($json_data);

            $social = (isset($attributes['social']) && $attributes['social']) ? $attributes['social'] : null;

            $locale_param = array('request_locale' => get_locale());

            $data = array(
                'login_url' => URLBuilder::getUrlLogin($scope, $social, null, array(), $state)
                    . '&' . http_build_query($locale_param, '', '&')
            );

            $data['is_user_logged'] = Identity::isConnected();

            $data['text'] = (isset($attributes['text']) && $attributes['text'])
                ? $attributes['text']
                : null;

            // If there is more than one hooks attached to this action then we deletegate render to these hooks,
            // else we should generate a basic control layer.
            (has_action('druid_auth_controls_login'))
                ? do_action('druid_auth_controls_login', $data)
                : Render_Service::render('public/auth-controls-login', $data);

        } catch (\Exception $e) {

            Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e);

        }

        return ob_get_clean();
    }
}