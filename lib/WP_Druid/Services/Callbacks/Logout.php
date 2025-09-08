<?php namespace WP_Druid\Services\Callbacks;

use Genetsis\Identity;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Front\Collections\Callbacks\Post_Login_Parameters;
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
        IdentityFactory::init(true);

        // Druid logout.
        if (Identity::isConnected())
            Identity::logoutUser();

        $state = Query_Vars_Service::find(Post_Login_Parameters::STATE, null);
        $pageToRedirect = $this->processState($state);

        // WP logout.
        Users_Service::logout();

        wp_safe_redirect($pageToRedirect);
        exit();
    }
}