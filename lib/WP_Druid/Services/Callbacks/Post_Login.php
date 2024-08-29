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
        $pageToRedirect = home_url();
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

            // Process the 'state' parameter to get the redirection URL
            $pageToRedirect = $this->processState($state);

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
                if ($code) {
                    Identity::authorizeUser($code, $scope);
                    if (!Identity::isConnected()) {
                        throw new Callback_Exception(__('We cannot authorize the user with the current code.', WPDR_LANG_NS));
                    }
                    Users_Service::login(UserApi::getUserLogged());
                }
            }
        } catch (Create_User_Exception $e) {
            Render_Service::render('public/error-page', array('message' => $e->getMessage())); // This view ends WP.
        } catch (Login_User_Exception $e) {
            Render_Service::render('public/error-page', array('message' => $e->getMessage())); // This view ends WP.
        } catch (\Exception $e) {
            Render_Service::render('public/error-page', array('message' => __('An unknown error prevented us to identify you on the platform. Please try again.', WPDR_LANG_NS))); // This view ends WP.
        }

        wp_safe_redirect($pageToRedirect);
        exit();
    }

    private function processState($state)
    {
        // Initialize pageToRedirect with default value
        $pageToRedirect = SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url());

        // Decoding the 'state' parameter if it exists
        if ($state) {
            try {
                $json_data = base64_decode($state);
                $data = json_decode($json_data, true);

                if (is_array($data)) {
                    $pageToRedirect = $data['pageToRedirect'] ?? $pageToRedirect;
                    // Encode 'state' attribute and append to the URL if it exists
                    if (isset($data['state'])) {
                        $state_attr_base64 = base64_encode(json_encode(['state' => $data['state']]));
                        $pageToRedirect = add_query_arg('state', $state_attr_base64, $pageToRedirect);
                    }
                } else {
                    // Log error and keep the default URL
                    Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', 'Invalid JSON data in state: ' . $json_data);
                }
            } catch (\Exception $e) {
                // Log error and keep the default URL
                Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', 'State decoding error: ' . $e->getMessage());
            }
        }

        return $pageToRedirect;
    }


}
