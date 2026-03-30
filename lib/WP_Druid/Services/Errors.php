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
        $db = new DB();
        $db->initialize_wpdb_tables();

        $dao_config = new ConfigDAO();
        $log_level = strtoupper((string) $dao_config->getLogLevel());

        if ($log_level === '') {
            $log_level = 'ERROR';
        }

        if ((defined('WP_DEBUG') && WP_DEBUG) || in_array($log_level, ['DEBUG', 'ERROR'], true)) {
            if ($error instanceof WP_Error) {
                $code = $error->get_error_code();
                $message = $error->get_error_message();
            } elseif ($error instanceof \Exception) {
                $code = $error->getCode();
                $message = $error->getMessage();
            } else {
                $code = null;
                $message = (string) $error;
            }

            global $wpdb;
            $table = $wpdb->druid_error_logs ?: ($wpdb->prefix . 'druid_error_logs');
            $now = current_time('mysql');

            $wpdb->insert(
                $table,
                [
                    'section'   => $section,
                    'logged_at' => $now,
                    'code'      => (string) $code,
                    'message'   => $message,
                ],
                ['%s', '%s', '%s', '%s']
            );

            if ($wpdb->last_error) {
                error_log('WP_Druid error log insert failed: ' . $wpdb->last_error);
            }
        }
    }
}
