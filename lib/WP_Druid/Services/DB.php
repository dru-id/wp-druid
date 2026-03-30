<?php namespace WP_Druid\Services;

/**
 * @package WP Druid
 */
class DB
{
    private function get_charset_collate()
    {
        global $wpdb;

        return $wpdb->get_charset_collate();
    }

    public function initialize_wpdb_tables()
    {
        global $wpdb;

        $wpdb->druid_user        = $wpdb->prefix . 'druid_user';
        $wpdb->druid_error_logs  = $wpdb->prefix . 'druid_error_logs';
        $wpdb->druid_config      = $wpdb->prefix . 'druid_config';
    }

    public function check_update()
    {
        $current = (int) get_option('druid_db_version');
        if ($current < (int) DRUID_DB_VERSION) {
            $this->install_db();
        }
    }

    public function install_db()
    {
        global $wpdb;
        $legacy_druid_log_table = $wpdb->prefix . 'druid_log';

        $this->initialize_wpdb_tables();
        $charset_collate = $this->get_charset_collate();

        $sql = [];

        $sql[] = "CREATE TABLE {$wpdb->druid_user} (
            druid_id VARCHAR(255) NOT NULL,
            wp_id BIGINT(20) UNSIGNED NOT NULL,
            druid_obj TEXT,
            last_update DATETIME,
            PRIMARY KEY (druid_id),
            KEY wp_id (wp_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->druid_error_logs} (
            id INT(11) NOT NULL AUTO_INCREMENT,
            logged_at DATETIME NOT NULL,
            section VARCHAR(255),
            code VARCHAR(255),
            message TEXT,
            PRIMARY KEY (id),
            KEY logged_at (logged_at)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->druid_config} (
            client_id VARCHAR(255) NOT NULL,
            client_secret VARCHAR(255) NOT NULL,
            entry_point VARCHAR(1000) NOT NULL,
            log_level VARCHAR(10) NOT NULL,
            callback VARCHAR(255),
            environment VARCHAR(50) NOT NULL,
            log_path VARCHAR(255) NOT NULL,
            cache_path VARCHAR(255) NOT NULL,
            domain VARCHAR(255) NOT NULL DEFAULT '',
            PRIMARY KEY (client_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ($sql as $s) {
            dbDelta($s);
        }

        $wpdb->query("DROP TABLE IF EXISTS {$legacy_druid_log_table}");

        update_option('druid_db_version', (int) DRUID_DB_VERSION);
    }

    public function clean_db()
    {
        global $wpdb;
        $this->initialize_wpdb_tables();

        $wpdb->query("TRUNCATE TABLE {$wpdb->druid_error_logs}");
    }

    public function remove_db()
    {
        global $wpdb;
        $this->initialize_wpdb_tables();
        $legacy_druid_log_table = $wpdb->prefix . 'druid_log';

        // Si quieres eliminar definitivamente:
        $wpdb->query("DROP TABLE IF EXISTS {$legacy_druid_log_table}");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->druid_error_logs}");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->druid_user}");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->druid_config}");

        delete_option('druid_db_version');
        delete_option('druid_plugin_version');
    }
}
