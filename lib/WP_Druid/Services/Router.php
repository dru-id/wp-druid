<?php namespace WP_Druid\Services;

use WP_Druid\Services\Callbacks\Post_Login;
use WP_Druid\Services\Callbacks\Logout;
use WP_Druid\Services\Callbacks\Pub_Sub_Hubbub;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Collections\Router\Router_Parameters;
use WP_Druid\Collections\Callbacks\Post_Login_Parameters;
use WP_Druid\Utils\Session\Services\SessionManager;

/**
 * Here is where you will register all of the routes accepted by this plugin.
 *
 * @package WP Druid
 */
class Router
{

    public function init()
    {
        add_filter('query_vars', function ($vars) {
                $vars[] = Router_Parameters::ACTION;
                $vars[] = Post_Login_Parameters::CODE;
                $vars[] = Post_Login_Parameters::SCOPE;
                $vars[] = Post_Login_Parameters::STATE;
                $vars[] = Post_Login_Parameters::ERROR;
                $vars[] = Post_Login_Parameters::ERROR_DESCRIPTION;
                return $vars;
            });

        add_action('parse_request', function ($wp) {
                $action = null;
                if (isset($_REQUEST['error']) && ($_REQUEST['error'] == Router_Parameters::ACTION_USER_CANCEL)) {
                    $action = Router_Parameters::ACTION_USER_CANCEL;
                } elseif (isset($wp->query_vars[Router_Parameters::ACTION]) && $wp->query_vars[Router_Parameters::ACTION]) {
                    $action = $wp->query_vars[Router_Parameters::ACTION];
                } else {
                    SessionManager::set(WPDR_CURRENT_LANGUAGE_CODE_SESSION_KEY, get_locale());
                }

                try {
                    switch ($action) {
                        case Router_Parameters::ACTION_USER_CANCEL: // When users push back buttons.
                            /*if (isset($wp->query_vars['state']) && is_user_logged_in()) {
                                header('Location: '.$_GET['state']);
                                exit();
                            }*/
                            SessionManager::set(WPDR_USER_CANCEL_ACTION_SESSION_KEY, true);
                            //echo SessionManager::get_and_forget(WPDR_CUSTOM_RETURN_URL_SESSION_KEY, SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url()));
                            wp_safe_redirect(SessionManager::get_and_forget(WPDR_CUSTOM_RETURN_URL_SESSION_KEY, SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url())));
                            exit();
                            break;

                        case Router_Parameters::ACTION_POSTLOGIN:
                            druid_x(new Post_Login())->run();
                            break;

                        case Router_Parameters::ACTION_LOGOUT:
                            druid_x(new Logout())->run();
                            break;

                        case Router_Parameters::ACTION_PUBSUBHUBBUB:
                            druid_x(new Pub_Sub_Hubbub())->run();
                            break;
                    }

                    // Save current URL to redirect user after Druid action (post-login, logout, ...)
                    SessionManager::set(WPDR_PREVIOUS_URL_SESSION_KEY, esc_url_raw(home_url(add_query_arg(null, null))));

                } catch (\Exception $e) {

                    Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e->getMessage());
                    wp_redirect(home_url());
                    exit();

                }
            });
    }

    /**
     * Setup custom rewrite rules expected by this plugin.
     *
     * @return void
     */
    public function add_rewrite_rules()
    {
        // TODO: these callbacks should be configured from admin panel.
        add_rewrite_tag( '%' . Router_Parameters::ACTION . '%', '([^&]+)' );
        add_rewrite_rule('actions/callback', 'index.php?' . Router_Parameters::ACTION . '=post-login', 'top');
        add_rewrite_rule('actions/logout', 'index.php?' . Router_Parameters::ACTION . '=logout', 'top');
        add_rewrite_rule('actions/pubsubhub', 'index.php?' . Router_Parameters::ACTION . '=pubsubhub', 'top');
    }
}