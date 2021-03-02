<?php defined('WP_UNINSTALL_PLUGIN') or exit('Direct access not allowed');

require __DIR__.'/lib/WP_Druid/Services/DB.php';

$db = new \WP_Druid\Services\DB();
$db->remove_db();