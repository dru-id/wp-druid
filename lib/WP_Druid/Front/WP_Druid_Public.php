<?php namespace WP_Druid\Front;

use Genetsis\Identity;
use WP_Druid\Factory\IdentityFactory;
use WP_Druid\Services\Callbacks\Logout as Logout_Callback;

class WP_Druid_Public
{
    protected $locale;

    public function init() {
        $this->enque_media();

        druid_x(new \WP_Druid\Services\Installer())->loaded();

        add_filter( 'the_excerpt', 'shortcode_unautop');
        add_filter( 'the_excerpt', 'do_shortcode');
        add_filter( 'get_the_excerpt', 'do_shortcode', 5 );

        IdentityFactory::init(true);

        add_action('init', function() {
            druid_x(new Router())->init();

            // TODO: Forces user to logout if is logged in DruID but logged in Wordpress.
            if (!is_user_logged_in() && Identity::isConnected()) {
                Identity::logoutUser();

                Identity::isConnected()
                    ? do_action(WPDR_ACTION_USER_IS_LOGGED)
                    : do_action(WPDR_ACTION_USER_IS_NOT_LOGGED);
            }
        });

        add_action('wp_logout', function(){ druid_x(new Logout_Callback())->run(); }, 10, 0);
    }

    private function enque_media() {
        add_action('wp_enqueue_scripts', function(){
            //TODO: url sso javascript loaded from wp admin properties
            //if (!Identity::isConnected())
            //    wp_enqueue_script('wpdr-login-sso', 'https://login-test.pernod-ricard-espana.com/login/sso');

        });
    }
}