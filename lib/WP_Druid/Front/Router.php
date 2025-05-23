<?php namespace WP_Druid\Front;

use Genetsis\Identity;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Front\Collections\Callbacks\Post_Login_Parameters;
use WP_Druid\Front\Collections\Router\Router_Parameters;
use WP_Druid\Services\Callbacks\Logout;
use WP_Druid\Services\Callbacks\Post_Login;
use WP_Druid\Services\Callbacks\Pub_Sub_Hubbub;
use WP_Druid\Utils\Session\Services\SessionManager;
use WP_Druid\Services\Errors as Errors_Service;


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
                }

                try {

                    switch ($action) {
                        case Router_Parameters::ACTION_USER_CANCEL: // When users push back buttons.
                            IdentityFactory::init(true);
                            if(!Identity::isConnected() && is_user_logged_in()) {
                                druid_x(new Logout())->run();
                            } else {
                                wp_safe_redirect(SessionManager::get_and_forget(WPDR_PREVIOUS_URL_SESSION_KEY, home_url()));
                                exit();

                            }
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
                    SessionManager::set(WPDR_PREVIOUS_URL_SESSION_KEY, esc_url_raw(home_url($_SERVER['REQUEST_URI'])));

                } catch (\Exception $e) {

                    Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e->getMessage());
                    wp_redirect(home_url());
                    exit();

                }
            });

    }

    public function remove_rewrite_rules() {
        remove_rewrite_tag('%' . Router_Parameters::ACTION . '%');
    }

    /**
     * Setup custom rewrite rules expected by this plugin.
     *
     * @return void
     */
    public function add_rewrite_rules()
    {
        add_rewrite_tag( '%' . Router_Parameters::ACTION . '%', '([^&]+)' );
        add_rewrite_rule('actions/callback', 'index.php?' . Router_Parameters::ACTION . '=post-login', 'top');

//        add_rewrite_rule('druid-actions/callback', 'index.php?' . Router_Parameters::ACTION . '=post-login', 'top');
        add_rewrite_rule('druid-actions/logout', 'index.php?' . Router_Parameters::ACTION . '=logout', 'top');
        //add_rewrite_rule('druid-actions/pubsubhub', 'index.php?' . Router_Parameters::ACTION . '=pubsubhub', 'top');
    }

    /**
     * Redirect
     *
     * @since    1.0.0
     */
    public static function custom_redirect( $page, $admin_notice, $response ) {

        wp_redirect( esc_url_raw( add_query_arg( array(
                'druid_admin_add_notice' => $admin_notice,
                'druid_response' => $response,
            ),
            admin_url('admin.php?page='. $page)
        )));
    }

}