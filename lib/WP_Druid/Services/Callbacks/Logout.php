<?php namespace WP_Druid\Services\Callbacks;

use Genetsis\Identity;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Front\Collections\Callbacks\Post_Login_Parameters;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Services\Shortcodes as Shortcodes_Service;
use WP_Druid\Services\Users as Users_Service;
use WP_Druid\Utils\Session\Services\SessionManager;
use WP_Druid\Contracts\Callbacks\Callbackable as CallbackContract;
use WP_Druid\Utils\Wp\Services\Query_Vars as Query_Vars_Service;

/**
 * @package WP Druid
 */
class Logout extends Callback_Base_Service implements CallbackContract
{
    public function run()
    {
        $pageToRedirect = home_url();

        try {
            IdentityFactory::require_initialized(true);

            // Druid logout.
            if (Identity::isConnected()) {
                Identity::logoutUser();
            }

            $state = Query_Vars_Service::find(Post_Login_Parameters::STATE, null);
            $pageToRedirect = $this->processState($state);

            // WP logout.
            Users_Service::logout();
        } catch (\Throwable $e) {
            Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e);
        }

        wp_safe_redirect($pageToRedirect);
        exit();
    }

    private function processState($state)
    {
        return Shortcodes_Service::get_page_to_redirect(
            $state,
            SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url())
        );
    }
}
