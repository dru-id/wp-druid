<?php namespace WP_Druid\Services;

/**
 * @package WP Druid
 */
class DB
{
    public function initialize_wpdb_tables()
    {
        global $wpdb;

        $wpdb->druid_user        = $wpdb->prefix . 'druid_user';
        $wpdb->druid_log         = $wpdb->prefix . 'druid_log';
        $wpdb->druid_error_logs  = $wpdb->prefix . 'druid_error_logs';
        $wpdb->druid_config      = $wpdb->prefix . 'druid_config';
    }

    public function check_update()
    {
        $current = (int) get_option('druid_db_version');
        if ($current !== (int) DRUID_DB_VERSION) {
            $this->install_db();
        }
    }

    public function install_db()
    {
        global $wpdb;

        $this->initialize_wpdb_tables();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = [];

        $sql[] = "CREATE TABLE {$wpdb->druid_log} (
            id INT(11) NOT NULL AUTO_INCREMENT,
            event VARCHAR(100) NOT NULL,
            level VARCHAR(100) NOT NULL DEFAULT 'notice',
            description TEXT,
            details LONGTEXT,
            logtime INT(11) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->druid_user} (
            druid_id VARCHAR(255) NOT NULL,
            wp_id BIGINT(20) UNSIGNED NOT NULL,
            druid_obj TEXT,
            last_update DATETIME,
            PRIMARY KEY (druid_id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->druid_error_logs} (
            id INT(11) NOT NULL AUTO_INCREMENT,
            logged_at DATETIME NOT NULL,
            section VARCHAR(255),
            code VARCHAR(255),
            message TEXT,
            PRIMARY KEY (id)
        ) $charset_collate;";

        $sql[] = "CREATE TABLE {$wpdb->druid_config} (
            client_id VARCHAR(100) NOT NULL,
            client_secret VARCHAR(100) NOT NULL,
            entry_point VARCHAR(1000) NOT NULL,
            log_level VARCHAR(10) NOT NULL,
            callback VARCHAR(200),
            environment VARCHAR(6) NOT NULL,
            log_path VARCHAR(200) NOT NULL,
            cache_path VARCHAR(200) NOT NULL,
            PRIMARY KEY (client_id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ($sql as $s) {
            dbDelta($s);
        }

        update_option('druid_db_version', (int) DRUID_DB_VERSION);
    }

    public function clean_db()
    {
        global $wpdb;
        $this->initialize_wpdb_tables();

        $wpdb->query("TRUNCATE TABLE {$wpdb->druid_log}");
        $wpdb->query("TRUNCATE TABLE {$wpdb->druid_error_logs}");
        $wpdb->query("TRUNCATE TABLE {$wpdb->druid_config}");
    }

    public function remove_db()
    {
        global $wpdb;
        $this->initialize_wpdb_tables();

        // Si quieres eliminar definitivamente:
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->druid_log}");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->druid_error_logs}");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->druid_user}");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->druid_config}");
    }
}
