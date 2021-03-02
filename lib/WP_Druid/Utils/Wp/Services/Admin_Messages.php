<?php namespace WP_Druid\Utils\Wp\Services;

/**
 * @package WP Druid
 */
class Admin_Messages
{
    /**
     * @param string $message
     * @param boolean $return
     * @return string|void If $return is TRUE then returns the final string.
     */
	public static function success($message, $return = false)
    {
        $message = self::generate_message($message, 'success');
        if ($return) {
            return $message;
        }
        echo $message;
	}

    /**
     * @param string $message
     * @param boolean $return
     * @return string|void If $return is TRUE then returns the final string.
     */
    public static function error($message, $return = false)
    {
        $message = self::generate_message($message, 'error');
        if ($return) {
            return $message;
        }
        echo $message;
    }

    /**
     * @param string $message
     * @param string $class_type Accepts: success, error
     * @return string
     */
    private static function generate_message($message, $class_type = 'success')
    {
        return '<div class="notice notice-' . $class_type . ' is-dismissible"><p>' . $message . '</p></div>';
    }
}
