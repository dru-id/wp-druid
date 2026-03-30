<?php namespace WP_Druid\Utils\Session\Services;

/**
 * @package WP Druid
 */
class SessionManager
{
    /**
     * Starts the PHP session when possible.
     *
     * @return bool
     */
    public static function ensure_started()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        if (session_status() === PHP_SESSION_DISABLED) {
            return false;
        }

        if (headers_sent($file, $line)) {
            error_log(sprintf('WP_Druid session_start skipped because headers were already sent in %s:%d', $file, $line));
            return false;
        }

        return @session_start();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        if ($key && self::ensure_started() && isset($_SESSION)) {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        if (!self::ensure_started()) {
            return $default;
        }

        return ($key && isset($_SESSION) && array_key_exists($key, $_SESSION) && $_SESSION[$key])
            ? $_SESSION[$key]
            : $default;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get_and_forget($key, $default = null)
    {
        $temp = self::get($key, $default);
        self::remove($key);
        return $temp;
    }

    /**
     * @param string $key
     * @return boolean
     */
    public static function has($key)
    {
        if (!self::ensure_started()) {
            return false;
        }

        return ($key && isset($_SESSION) && array_key_exists($key, $_SESSION));
    }

    /**
     * @param string $key
     * @return void
     */
    public static function remove($key)
    {
        if ($key && self::ensure_started() && isset($_SESSION) && array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }
}
