<?php namespace WP_Druid\Services;

use WP_Druid\DAO\ConfigDAO;
use WP_Error;

/**
 * @package WP Druid
 */
class Errors
{
    /**
     * Logs and rethrow the exception.
     *
     * @param \Exception $e
     * @param string $section
     * @throws \Exception
     */
    public static function throw_exception(\Exception $e, $section = null)
    {
        self::log_error($section, $e);
        throw $e;
    }

    /**
     * Stores the error into database.
     *
     * @param string $section
     * @param \WP_Error|\Exception|string $error
     * @return void
     */
    public static function log_error($section, $error)
    {

        $dao_config = new ConfigDAO();

        if (defined('WP_DEBUG') || in_array($dao_config->getLogLevel(), ['Debug', 'Error'], true)) {
            if ($error instanceof WP_Error) {
                $code = $error->get_error_code();
                $message = $error->get_error_message();
            } elseif ($error instanceof \Exception) {
                $code = $error->getCode();
                $message = $error->getMessage();
            } else {
                $code = null;
                $message = $error;
            }

            global $wpdb;
            $wpdb->insert(
                $wpdb->druid_error_logs,
                array(
                    'section' => $section,
                    'date' => date('c'),
                    'code' => $code,
                    'message' => $message,
                ),
                array('%s', '%s', '%s', '%s')
            );
        }
    }
}
