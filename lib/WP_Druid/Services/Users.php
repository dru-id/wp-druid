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
            $is_new_user = false;

            if ($wp_user instanceof \WP_User) {
                $update_result = static::update_wp_user($wp_user, $druid_user_data);
                static::synchronize_local_druid_user(
                    $druid_id,
                    $wp_user->ID,
                    $druid_user_data,
                    Login_User_Exception::class,
                    __('We have had problems identifying on the web. Please try again.', WPDR_LANG_NS)
                );

                if (!empty($update_result['updated'])) {
                    do_action(WPDR_ACTION_POST_EDIT_ACCOUNT, array(
                        'wp_user_id' => $update_result['wp_user_id'],
                        'druid_user' => $druid_user_data,
                    ));
                }
            } else {
                $wp_user_id = static::create($druid_user_data);
                $wp_user = get_user_by('id', $wp_user_id);
                if (!($wp_user instanceof \WP_User)) {
                    throw new Login_User_Exception('There should be a registered user but can not retrieve data from it.');
                }
                $is_new_user = true;
            }

            static::complete_login($wp_user, $druid_user_data, $is_new_user);
        } catch (Create_User_Exception $e) {
            throw $e;
        } catch (Login_User_Exception $e) {
            Errors_Service::log_error(__CLASS__ . ' (' . __LINE__ . ')', $e->getMessage());
            throw new Login_User_Exception(__('We have had problems identifying on the web. Please try again.', WPDR_LANG_NS), $e->getCode(), $e);
        } catch (\Throwable $e) {
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

        static::create_local_druid_user($druid_user_data->user->oid, $wp_user_id, $druid_user_data);

        do_action(WPDR_ACTION_POST_REGISTER, array('wp_user_id' => $wp_user_id, 'druid_user' => $druid_user_data));

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
        static::synchronize_local_druid_user(
            $druid_id,
            $wp_id,
            $druid_user_data,
            Create_User_Exception::class,
            __('We have had problems registering on the web.', WPDR_LANG_NS)
        );
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

        $wp_user = static::find_user($druid_user_data->user->oid);
        $update_result = static::update_wp_user($wp_user, $druid_user_data);

        static::synchronize_local_druid_user(
            $druid_user_data->user->oid,
            $update_result['wp_user_id'],
            $druid_user_data,
            \Exception::class,
            __('We have had problems updating the user data.', WPDR_LANG_NS)
        );

        if (!empty($update_result['updated'])) {
            do_action(WPDR_ACTION_POST_EDIT_ACCOUNT, array(
                'wp_user_id' => $update_result['wp_user_id'],
                'druid_user' => $druid_user_data,
            ));
        }
    }

    /**
     * Gets the canonical DruID identifier linked to a WordPress user.
     *
     * @param integer $wp_user_id
     * @return string|null
     */
    public static function get_druid_id_by_wp_user_id($wp_user_id)
    {
        $wp_user_id = absint($wp_user_id);
        if (!$wp_user_id) {
            return null;
        }

        $rows = static::get_druid_user_rows_by_wp_user_id($wp_user_id, 2);
        if (empty($rows)) {
            return null;
        }

        if (count($rows) > 1) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', sprintf('Multiple DruID identifiers are linked to WordPress user "%d".', $wp_user_id));
            return null;
        }

        return (isset($rows[0]->druid_id) && $rows[0]->druid_id)
            ? $rows[0]->druid_id
            : null;
    }

    /**
     * Gets the canonical DruID identifier linked to the current WordPress user.
     *
     * @return string|null
     */
    public static function get_current_user_druid_id()
    {
        return static::get_druid_id_by_wp_user_id(get_current_user_id());
    }

    /**
     * Checks whether a WordPress user has a valid DruID link.
     *
     * @param integer|null $wp_user_id
     * @return bool
     */
    public static function has_druid_link($wp_user_id = null)
    {
        if (is_null($wp_user_id)) {
            $wp_user_id = get_current_user_id();
        }

        return !is_null(static::get_druid_id_by_wp_user_id($wp_user_id));
    }

    /**
     * Internal function to update WP User with DruID user data.
     *
     * @param \WP_User $wp_user
     * @param \stdClass $druid_user_data
     * @return array
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

        $wp_user_mail = $druid_user_data->user->oid . '@dru-id.internal';
        if (isset($druid_user_data->user->oid) && $druid_user_data->user->oid
            && ($druid_user_data->user->user_ids->email->value != $wp_user_mail)) {
            $wp_user->user_email = $druid_user_data->user->oid . '@dru-id.internal';
            $update_user_data = true;
        }

        if ($update_user_data) {
            $wp_user = wp_update_user($wp_user);
            if (is_wp_error($wp_user)) {
                Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $wp_user);
                throw new \Exception(__('We have had problems updating the user data.', WPDR_LANG_NS));
            }

            return array(
                'wp_user_id' => $wp_user,
                'updated' => true,
            );
        }

        return array(
            'wp_user_id' => $wp_user->ID,
            'updated' => false,
        );
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

        $result = $wpdb->get_row($wpdb->prepare('SELECT u.* FROM ' . $wpdb->users . ' u INNER JOIN ' . $wpdb->druid_user . ' du ON du.wp_id = u.ID WHERE du.druid_id = %s LIMIT 1', $druid_id));
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

    /**
     * Completes the WordPress login and exposes the public post-login hook.
     *
     * @param \WP_User $wp_user
     * @param \stdClass $druid_user_data
     * @param bool $is_new_user
     * @return void
     */
    protected static function complete_login($wp_user, $druid_user_data, $is_new_user = false)
    {
        $remember_users_session = true; // TODO: Should we keep user logged between sessions?

        wp_set_current_user($wp_user->ID, $wp_user->user_login);
        wp_set_auth_cookie($wp_user->ID, $remember_users_session);

        do_action('wp_login', $wp_user->user_login, $wp_user);
        do_action(WPDR_ACTION_POST_LOGIN, array(
            'wp_user_id' => $wp_user->ID,
            'wp_user' => $wp_user,
            'druid_id' => $druid_user_data->user->oid,
            'druid_user' => $druid_user_data,
            'is_new_user' => (bool) $is_new_user,
        ));
    }

    /**
     * Persists the canonical DruID <-> WordPress relation without allowing reassignments.
     *
     * @param string $druid_id
     * @param integer $wp_id
     * @param \stdClass $druid_user_data
     * @param string $exception_class
     * @param string $message
     * @return void
     * @throws \Exception
     */
    protected static function synchronize_local_druid_user($druid_id, $wp_id, $druid_user_data, $exception_class, $message)
    {
        try {
            static::persist_local_druid_user($druid_id, $wp_id, $druid_user_data);
        } catch (\RuntimeException $e) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e->getMessage());
            throw new $exception_class($message, 0, $e);
        }
    }

    /**
     * Writes or refreshes the canonical DruID <-> WordPress relation.
     *
     * @param string $druid_id
     * @param integer $wp_id
     * @param \stdClass $druid_user_data
     * @return void
     */
    protected static function persist_local_druid_user($druid_id, $wp_id, $druid_user_data)
    {
        global $wpdb;

        $wp_id = absint($wp_id);
        if (!$druid_id || !$wp_id) {
            throw new \RuntimeException('Cannot persist an empty DruID relation.');
        }

        $current_relation = static::get_druid_user_row_by_druid_id($druid_id);
        if ($current_relation && (int) $current_relation->wp_id !== $wp_id) {
            throw new \RuntimeException(sprintf('DruID identifier "%s" is already linked to WordPress user "%d".', $druid_id, $current_relation->wp_id));
        }

        $wp_relations = static::get_druid_user_rows_by_wp_user_id($wp_id, 2);
        if (count($wp_relations) > 1) {
            throw new \RuntimeException(sprintf('WordPress user "%d" has multiple DruID relations and cannot be synchronized safely.', $wp_id));
        }

        if (!empty($wp_relations) && $wp_relations[0]->druid_id !== $druid_id) {
            throw new \RuntimeException(sprintf('WordPress user "%d" is already linked to DruID identifier "%s".', $wp_id, $wp_relations[0]->druid_id));
        }

        $row_data = array(
            'druid_obj' => wp_json_encode($druid_user_data),
            'last_update' => current_time('mysql'),
        );

        if ($current_relation) {
            $status = $wpdb->update(
                $wpdb->druid_user,
                $row_data,
                array(
                    'druid_id' => $druid_id,
                    'wp_id' => $wp_id,
                ),
                array('%s', '%s'),
                array('%s', '%d')
            );
        } else {
            $status = $wpdb->insert(
                $wpdb->druid_user,
                array_merge(
                    array(
                        'druid_id' => $druid_id,
                        'wp_id' => $wp_id,
                    ),
                    $row_data
                ),
                array('%s', '%d', '%s', '%s')
            );
        }

        if ($status === false) {
            throw new \RuntimeException('Cannot persist the canonical DruID relation.');
        }
    }

    /**
     * Gets the canonical persistence row for a DruID identifier.
     *
     * @param string $druid_id
     * @return object|null
     */
    protected static function get_druid_user_row_by_druid_id($druid_id)
    {
        global $wpdb;

        $result = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->druid_user . ' WHERE druid_id = %s LIMIT 1', $druid_id));
        if (is_null($result) || $result instanceof \WP_Error) {
            if ($result instanceof \WP_Error) {
                Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $result);
            }

            return null;
        }

        return $result;
    }

    /**
     * Gets persistence rows linked to a WordPress user.
     *
     * @param integer $wp_user_id
     * @param integer $limit
     * @return array
     */
    protected static function get_druid_user_rows_by_wp_user_id($wp_user_id, $limit = 1)
    {
        global $wpdb;

        $wp_user_id = absint($wp_user_id);
        $limit = max(1, absint($limit));

        if (!$wp_user_id) {
            return array();
        }

        $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM ' . $wpdb->druid_user . ' WHERE wp_id = %d LIMIT %d', $wp_user_id, $limit));
        if (!is_array($results)) {
            if ($results instanceof \WP_Error) {
                Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $results);
            }

            return array();
        }

        return $results;
    }
}
