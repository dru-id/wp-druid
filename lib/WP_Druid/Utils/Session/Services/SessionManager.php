<?php namespace WP_Druid\Utils\Session\Services;

/**
 * @package WP Druid
 */
class SessionManager
{
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value)
    {
        if ($key && isset($_SESSION)) {
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
        return ($key && isset($_SESSION) && array_key_exists($key, $_SESSION));
    }

    /**
     * @param string $key
     * @return void
     */
    public static function remove($key)
    {
        if ($key && isset($_SESSION) && array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }
}