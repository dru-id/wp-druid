<?php namespace WP_Druid\Services\Callbacks;

use Genetsis\Identity;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Services\Users as Users_Service;
use WP_Druid\Utils\Session\Services\SessionManager;
use WP_Druid\Contracts\Callbacks\Callbackable as CallbackContract;

/**
 * @package WP Druid
 */
class Logout extends Callback_Base_Service implements CallbackContract
{
    public function run()
    {
        try {
            Identity::init(druid_get_current_client(), (is_admin() ? false : true));
            if (Identity::isConnected()) {
                Identity::logoutUser(); // Druid logout.
            }
            Users_Service::logout(); // WP logout.
        } catch (\Exception $e) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e->getMessage());
        }

        wp_safe_redirect(SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url()));
        exit();
    }
}