<?php namespace WP_Druid\Services\Callbacks;

use Genetsis\Identity;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Front\Collections\Callbacks\Post_Login_Parameters;
use WP_Druid\Services\Errors as Errors_Service;
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