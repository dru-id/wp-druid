<?php namespace WP_Druid\Utils\Wp\Services;

/**
 * @package WP Druid
 */
class Query_Vars
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function find($key, $default = null)
    {
        global $wp;

        if (isset($wp->query_vars[$key])) {
            return $wp->query_vars[$key];
        }

        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
        }

        return $default;
    }
}