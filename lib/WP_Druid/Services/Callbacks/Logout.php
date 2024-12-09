<?php namespace WP_Druid\Services\Callbacks;

use Genetsis\Identity;
use WP_Druid\Factory\IdentityFactory;
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
        IdentityFactory::init(true);

        // Druid logout.
        if (Identity::isConnected())
            Identity::logoutUser();

        // WP logout.
        Users_Service::logout();

        wp_safe_redirect(SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url()));
        exit();
    }
}