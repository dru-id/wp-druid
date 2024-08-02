<?php

/**
 * This file contains all constants used to define DruID actions that users can use to hook
 * their own functions.
 */

if (!defined('WPDR_ACTION__CONSTANTS__')) {
    define('WPDR_ACTION__CONSTANTS__', true);

    /**
     * Functions hooked to this action will be triggered if a DruID user is logged in, after Wordpress has
     * finished loading but before any header are sent to user's browser.
     */
    define('WPDR_ACTION_USER_IS_LOGGED', 'druid_user_is_logged');

    /**
     * Functions hooked to this action will be triggered if a DruID user is not logged in, after Wordpress has
     * finished loading but before any header are sent to user's browser.
     */
    define('WPDR_ACTION_USER_IS_NOT_LOGGED', 'druid_user_is_not_logged');

    /**
     * Fuctions hooked to this action will be triggered after a valid PubSubHubbub call is received, after Wordpress has
     * finished loading but before any header are sent to user's browser.
     *
     * These functions should expected one parameter with the payload received.
     */
    define('WPDR_ACTION_PUBSUBHUBBUB', 'druid_pubsubhubbub');

    /**
     * Functions hooked to this action will be triggered if the user is successfully logged in DruId.
     */
    define('WPDR_ACTION_POST_LOGIN', 'druid_post_login');

    /**
     * Functions hooked to this action will be triggered if the user is successfully created in DruId and in this site.
     *
     * An array is passed as first parameter with the following options:
     *      - wp_user_id (integer) Id of the new Wordpress user.
     *      - druid_user (object) Object with DruID user data.
     */
    define('WPDR_ACTION_POST_REGISTER', 'druid_post_register');

    /**
     * Functions hooked to this action will be triggered if the user has successfully updated its data on DruID and in this site.
     *
     * An array is passed as first parameter with the following options:
     *      - wp_user_id (integer) Id of the new Wordpress user.
     *      - druid_user (object) Object with DruID user data.
     */
    define('WPDR_ACTION_POST_EDIT_ACCOUNT', 'druid_post_edit_account');

    /**
     * Functions hooked to this action will be triggered if the user is successfully logged out from DruId.
     */
    define('WPDR_ACTION_POST_LOGOUT', 'druid_post_logout');

}