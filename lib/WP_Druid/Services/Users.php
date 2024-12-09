<?php namespace WP_Druid\Services;

use Hashids\Hashids;
use WP_Druid\Services\Errors as Errors_Service;
use WP_Druid\Exceptions\Users\Create_User_Exception;
use WP_Druid\Exceptions\Users\Login_User_Exception;

/**
 * @package WP Druid
 */
class Users
{
    /**
     * Executes the login process.
     *
     * @param \stdClass $druid_user_data
     * @return void
     *
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws Login_User_Exception
     * @throws Create_User_Exception
     */
    public static function login ($druid_user_data)
    {
        if (!($druid_user_data instanceof \stdClass)) {
            throw new \InvalidArgumentException('There are no user data.');
        }
        if (!isset($druid_user_data->user) || !isset($druid_user_data->user->oid) || !$druid_user_data->user->oid) {
            throw new \InvalidArgumentException('Invalid Druid user identifier.');
        }
        $druid_id = $druid_user_data->user->oid;

        try {
            // See if there is an user with this user identifier.
            $wp_user = static::find_user($druid_id);

            if ($wp_user instanceof \WP_User) {
                static::update_wp_user($wp_user, $druid_user_data);
                $remember_users_session = true; // TODO: Should we keep user logged between sessions?
                wp_set_current_user($wp_user->ID, $wp_user->user_login);
                wp_set_auth_cookie($wp_user->ID, $remember_users_session);
                do_action('wp_login', $wp_user->user_login, $wp_user);
            } else {
                $wp_user_id = static::create($druid_user_data);
                $wp_user_data = get_user_by('id', $wp_user_id);
                if (!($wp_user_data instanceof \WP_User)) {
                    throw new Login_User_Exception('There should be a registered user but can not retrieve data from it.');
                }
                $remember_users_session = true; // TODO: Should we keep user logged between sessions?
                wp_set_current_user($wp_user_data->ID, $wp_user_data->user_login);
                wp_set_auth_cookie($wp_user_data->ID, $remember_users_session);
                do_action('wp_login', $wp_user_data->user_login, $wp_user_data);
            }
        } catch (Create_User_Exception $e) {
            throw $e;
        } catch (Login_User_Exception $e) {
            Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e->getMessage());
            throw new Login_User_Exception(__('We have had problems identifying on the web. Please try again.', WPDR_LANG_NS), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Executes the process to create a new user into WP users table.
     *
     * @param \stdClass $druid_user_data
     * @return integer
     *
     * @throws \Exception
     * @throws Create_User_Exception
     */
    public static function create ($druid_user_data) // TODO: should be this method public?
    {
        $wp_user_data = array();

        // User's email.
        if (isset($druid_user_data->user->user_ids->email->value) && $druid_user_data->user->user_ids->email->value
            && isset($druid_user_data->user->user_ids->email->confirmed) && $druid_user_data->user->user_ids->email->confirmed) {

            $temp = $druid_user_data->user->oid . '@dru-id.internal';
        } else {
            $temp = md5(microtime(true)) . '@' . md5(microtime(true)) . '.com';
        }
        $wp_user_data['user_email'] = $temp;

        // Generates a random username.
        $temp = 'wdr-'.druid_x(new Hashids())->encode($druid_user_data->user->oid);
        while(username_exists($temp)) {
            $temp = $temp . rand(0,9);
        }
        $wp_user_data['user_login'] = $temp;

        // Random password.
        $wp_user_data['user_pass'] = wp_generate_password();

        // First and Last name.
        $wp_user_data['first_name'] = (isset($druid_user_data->user->user_data->name->value) && $druid_user_data->user->user_data->name->value)
            ? $druid_user_data->user->user_data->name->value
            : '';
        $wp_user_data['last_name'] = (isset($druid_user_data->user->user_data->surname->value) && $druid_user_data->user->user_data->surname->value)
            ? $druid_user_data->user->user_data->surname->value
            : '';

        // Display name.
        $temp = trim($wp_user_data['first_name'].' '.$wp_user_data['last_name']);
        $wp_user_data['display_name'] = $temp ?: $wp_user_data['user_login'];

        // Note: roles logic will be delegated to the site logic. See WPDR_ACTION_POST_REGISTER.
        $wp_user = get_user_by('email', $wp_user_data['user_email']);

        if (!$wp_user) {
            $wp_user_id = wp_insert_user($wp_user_data);
            if ($wp_user_id instanceof \WP_Error) {
                Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $wp_user_id);
                throw new Create_User_Exception(__('We have had problems registering on the web.', WPDR_LANG_NS));
            }
        } else {
            // Si ya existe el usuario, obtenemos su ID
            $wp_user_id = $wp_user->ID;
        }

        do_action(WPDR_ACTION_POST_REGISTER, array('wp_user_id' => $wp_user_id, 'druid_user' => $druid_user_data));

        static::create_local_druid_user($druid_user_data->user->oid, $wp_user_id, $druid_user_data);

        return $wp_user_id;
    }

    /**
     * Executes the process to create a new user into the WP Druid users database.
     *
     * @param string $druid_id
     * @param integer $wp_id
     * @param \stdClass $druid_user_data
     * @return void
     *
     * @throws Create_User_Exception
     */
    public static function create_local_druid_user ($druid_id, $wp_id, $druid_user_data)
    {
        global $wpdb;
        $status = $wpdb->insert(
            $wpdb->druid_user,
            array(
                'druid_id' => $druid_id,
                'wp_id' => $wp_id,
                'druid_obj' => json_encode($druid_user_data),
                'last_update' => date('c'),
            ),
            array('%s', '%d', '%s', '%s')
        );
        if ($status === false) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', 'Cannot create a druid user and link it to WP user.');
            throw new Create_User_Exception(__('We have had problems registering on the web.', WPDR_LANG_NS));
        }
    }

    /**
     * Updates WP User with DruID user data.
     *
     * @param \stdClass $druid_user_data
     * @return void
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public static function update($druid_user_data)
    {
        if (!isset($druid_user_data->user->oid) || !$druid_user_data->user->oid) {
            throw new \InvalidArgumentException('Invalid Druid user identifier.');
        }
        static::update_wp_user(static::find_user($druid_user_data->user->oid), $druid_user_data);
    }

    /**
     * Internal function to update WP User with DruID user data.
     *
     * @param \WP_User $wp_user
     * @param \stdClass $druid_user_data
     * @return void
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected static function update_wp_user($wp_user, $druid_user_data)
    {
        if (!($wp_user instanceof \WP_User)) {
            throw new \InvalidArgumentException(__('A WP_User object must be provided.', WPDR_LANG_NS));
        }

        $update_user_data = false;
        $update_display_name = false;

        if (isset($druid_user_data->user->user_data->name->value) && ($druid_user_data->user->user_data->name->value != $wp_user->first_name)) {
            $wp_user->first_name = $druid_user_data->user->user_data->name->value;
            $update_display_name = true;
        }

        if (isset($druid_user_data->user->user_data->surname->value) && ($druid_user_data->user->user_data->surname->value != $wp_user->last_name)) {
            $wp_user->last_name = $druid_user_data->user->user_data->surname->value;
            $update_display_name = true;
        }

        if ($update_display_name) {
            $temp = trim($wp_user->first_name . ' ' . $wp_user->last_name);
            $wp_user->display_name = $temp ?: $wp_user->user_login;
            $update_user_data = true;
        }

        if (isset($druid_user_data->user->user_ids->email->value) && $druid_user_data->user->user_ids->email->value
            && isset($druid_user_data->user->user_ids->email->confirmed) && $druid_user_data->user->user_ids->email->confirmed
            && ($druid_user_data->user->user_ids->email->value != $wp_user->user_email)) {

            $wp_user->user_email = $druid_user_data->user->oid . '@dru-id.internal';
            $update_user_data = true;
        }

        if ($update_user_data) {
            $wp_user = wp_update_user($wp_user);
            if (is_wp_error($wp_user)) {
                Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $wp_user);
                throw new \Exception(__('We have had problems updating the user data.', WPDR_LANG_NS));
            } else {
                do_action(WPDR_ACTION_POST_EDIT_ACCOUNT, array('wp_user_id' => $wp_user, 'druid_user' => $druid_user_data));
            }
        }
    }

    /**
     * Finds an user using an identifier.
     *
     * @param string $druid_id Druid user identifier.
     * @return \WP_User|null
     */
    public static function find_user ($druid_id)
    {
        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare('SELECT u.* FROM ' . $wpdb->users . ' u INNER JOIN ' . $wpdb->druid_user . ' du ON du.wp_id = u.id where du.druid_id = "%s" LIMIT 1', $druid_id));
        if (is_null($result)) {
            return null;
        } elseif ($result instanceof \WP_Error) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $result);
            return null;
        }

        $user = new \WP_User();
        $user->init($result);
        return $user;
    }

    /**
     * Removes associated data for a deleted Wordpress user.
     *
     * @param integer $wp_user_id
     * @return void
     */
    public static function delete_local_druid_user($wp_user_id)
    {
        global $wpdb;

        if ($wp_user_id) {
            $wpdb->delete($wpdb->druid_user, array('wp_id' => $wp_user_id), array('%d'));
        }
    }

    /**
     * Performs the logout process.
     *
     * @return void
     */
    public static function logout()
    {
        // We have to do it manually instead of calling "wp_logout" to avoid nested calls problem. The reason
        // is the "wp_logout" function triggers "wp_logout" action, and as we have hooked this method to that action
        // the final result is a nice infinite loop call.
        wp_destroy_current_session();
        wp_clear_auth_cookie();
    }
}
