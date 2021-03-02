<?php namespace WP_Druid\Services;

/**
 * @package WP Druid
 */
class DB
{
	public function initialize_wpdb_tables()
    {
        global $wpdb;

		$wpdb->druid_user = $wpdb->prefix.'druid_user';
		$wpdb->druid_log = $wpdb->prefix.'druid_log';
		$wpdb->druid_error_logs = $wpdb->prefix.'druid_error_logs';
	}

	public function check_update()
    {
		if ((int)get_site_option('druid_db_version') !== DRUID_DB_VERSION ) {
			$this->install_db();
		}
	}

	public function install_db()
    {
		global $wpdb;

		$this->initialize_wpdb_tables();

		$sql = array();

		$sql[] = "CREATE TABLE IF NOT EXISTS ".$wpdb->druid_log." (
					id INT(11) AUTO_INCREMENT NOT NULL,
					event VARCHAR(100) NOT NULL,
					level VARCHAR(100) NOT NULL DEFAULT 'notice',
					description TEXT,
					details LONGTEXT,
					logtime INT(11) NOT NULL,
					PRIMARY KEY  (id)
				);";

		$sql[] = "CREATE TABLE IF NOT EXISTS ".$wpdb->druid_user." (
					druid_id VARCHAR(100) NOT NULL,
					wp_id INT(11)  NOT NULL,
					druid_obj TEXT,
					last_update DATETIME,
					PRIMARY KEY  (druid_id)
				);";

		$sql[] = "CREATE TABLE IF NOT EXISTS ".$wpdb->druid_error_logs." (
					id INT(11) AUTO_INCREMENT NOT NULL,
					date DATETIME  NOT NULL,
					section VARCHAR(255),
					code VARCHAR(255),
					message TEXT,
					PRIMARY KEY  (id)
				);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		foreach ($sql as $s) {
			dbDelta($s);
		}
		update_site_option('druid_db_version', DRUID_DB_VERSION);
	}

	public function clean_db()
    {
		global $wpdb;

		$this->initialize_wpdb_tables();
		$sql = array();

		$sql[] = "TRUNCATE TABLE ".$wpdb->druid_log.";";
		$sql[] = "TRUNCATE TABLE ".$wpdb->druid_error_logs.";";

		foreach ( $sql as $s ) {
			$wpdb->query($s);
		}
	}

    public function remove_db()
    {
        global $wpdb;

        $this->initialize_wpdb_tables();
        $sql = array();

        $sql[] = "DROP TABLE ".$wpdb->druid_log.";";
        $sql[] = "DROP TABLE ".$wpdb->druid_error_logs.";";
        $sql[] = "DROP TABLE ".$wpdb->druid_user.";";

        foreach ( $sql as $s ) {
            $wpdb->query($s);
        }
    }
}
