<?php namespace WP_Druid\Admin\Services;

/**
 * @package WP Druid
 */
class Admin_Messages
{
    /**
     * @param string $message
     * @return string|void If $return is TRUE then returns the final string.
     */
	public static function success($message)
    {
        self::generate_message($message, 'updated');
	}

    /**
     * @param string $message
     * @return string|void If $return is TRUE then returns the final string.
     */
    public static function error($message)
    {
        self::generate_message($message, 'error');
    }

    /**
     * @param string $message
     * @param string $class_type Accepts: success, error
     * @return string
     */
    private static function generate_message($message, $class_type = 'updated')
    {
        add_settings_error(
            'wpdr-admin-messges',
            esc_attr( 'settings_updated' ),
            $message,
            $class_type
        );
    }
}
