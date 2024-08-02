<?php namespace WP_Druid\Services\Callbacks;

use Genetsis\core\OAuthConfig;
use Genetsis\URLBuilder;
use WP_Druid\Contracts\Callbacks\Callbackable as CallbackContract;
use Genetsis\UserApi;
use Genetsis\Identity;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Front\Collections\Callbacks\Post_Login_Parameters;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Services\Render as Render_Service;
use WP_Druid\Exceptions\Users\Create_User_Exception;
use WP_Druid\Exceptions\Users\Login_User_Exception;
use WP_Druid\Utils\Wp\Services\Query_Vars as Query_Vars_Service;
use WP_Druid\Services\Users as Users_Service;
use WP_Druid\Exceptions\Callbacks\Callback_Exception;
use WP_Druid\Utils\Session\Services\SessionManager;

/**
 * @package WP Druid
 */
class Post_Login extends Callback_Base_Service implements CallbackContract
{
    public function run()
    {
        try {
            IdentityFactory::init((is_admin() ? false : true));

            // Checks if the service has an error after login.
            $error = Query_Vars_Service::find(Post_Login_Parameters::ERROR, null);
            if ($error) {
                throw new Callback_Exception($error);
            }

            $code = Query_Vars_Service::find(Post_Login_Parameters::CODE, null);
            $scope = Query_Vars_Service::find(Post_Login_Parameters::SCOPE, Identity::getOAuthConfig()->getDefaultSection());
            $state = Query_Vars_Service::find(Post_Login_Parameters::STATE, null);

            if (Identity::isConnected()) {
                // At the time of this comment DruID does not tell us which was the action made by the user that triggers this action, so we
                // have to suppose it.
                if (!$code && (($user = UserApi::getUserLogged()) instanceof \stdClass) && isset($user->user->id)) {
                    UserApi::deleteCacheUser($user->user->id);
                    Users_Service::update(UserApi::getUserLogged());
                }
            } else {
                // Because DruID does not tell us which was the action made by the user then if there is no CODE defined we have to suppose
                // that user has come from the registration process.
                //TODO: get scope from state
                if ($code) {
                    Identity::authorizeUser($code, $scope);
                    if (!Identity::isConnected()) {
                        throw new Callback_Exception(__('We cannot authorize the user with the current code.', WPDR_LANG_NS));
                    }
                    Users_Service::login(UserApi::getUserLogged());

                    if(!Identity::checkUserComplete($scope)) {
                        wp_safe_redirect(URLBuilder::getUrlCompleteAccount($scope, null, $state));
                        exit();
                    }
                }
            }
        } catch (Create_User_Exception $e) {
            Render_Service::render('public/error-page', array('message' => $e->getMessage())); // This view ends WP.
        } catch (Login_User_Exception $e) {
            Render_Service::render('public/error-page', array('message' => $e->getMessage())); // This view ends WP.
        } catch (\Exception $e) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e->getMessage());
            Render_Service::render('public/error-page', array('message' => __('An unknown error prevented us to identify you on the platform. Please try again.', WPDR_LANG_NS))); // This view ends WP.
        }

        wp_safe_redirect(SessionManager::get_and_forget(WPDR_CUSTOM_RETURN_URL_SESSION_KEY, SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url())));
        exit();
    }
}
