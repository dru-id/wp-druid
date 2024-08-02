<?php namespace WP_Druid\Services;

/**
 * @package WP Druid
 */
class Render
{
    /**
     * @param string $page Path to template file. It must be a relative path from "views" folder.
     * @param array|null $data The data to pass to template.
     */
    public static function render($page, $data = null)
    {
        if (strpos($page, '.php') !== false) {
            $page = trim(str_replace('.php', '', $page));
        }
        if ($page) {
            $page = realpath(WPDR_PLUGIN_DIR . 'views/' . $page . '.php');
            if ($page && is_file($page)) {
                if (is_array($data) && !empty($data)) {
                    extract($data);
                }
                include $page;
            }
        }
    }
}
