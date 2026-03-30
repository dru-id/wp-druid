<?php namespace WP_Druid\DAO;

use WP_Druid\Models\Config;

class ConfigDAO extends DAO
{
    public static $table_name = "druid_config";

    /**
     * Config constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setFullTableName(ConfigDAO::$table_name);
    }

    public function get() {
        $result = $this->getWpdb()->get_row("SELECT * FROM " . $this->getFullTableName() . " LIMIT 1");

        return $this->setConfig($result);
    }

    public function getByClientID($client_id) {
        $query = $this->getWpdb()->prepare("SELECT * FROM " . $this->getFullTableName() . " WHERE `client_id` = %s", $client_id);
        $result = $this->getWpdb()->get_row($query);

        return $this->setConfig($result);
    }

    public function getLogLevel() {
        $query = "SELECT log_level FROM " . $this->getFullTableName() . " LIMIT 1";
        return $this->getWpdb()->get_var($query);
    }

    private function setConfig($result){
        $druid_config = new Config();
        if ($result != null) {
            $druid_config->setClientId($result->client_id)
                ->setClientSecret($result->client_secret)
                ->setCallback($result->callback)
                ->setEntryPoint($result->entry_point)
                ->setLogLevel($result->log_level)
                ->setLogPath($result->log_path)
                ->setCachePath($result->cache_path)
                ->setEnvironment($result->environment)
                ->setDomain($result->domain);
        }
        return $druid_config;
    }

    public function update(Config $config) {
        if ((empty($config->getClientId()))||(empty($config->getClientSecret()))) {
            throw new \Exception("Invalid Data");
        }

        $existing_config = $this->get();

        $data = array(
            'client_id' => $config->getClientId(),
            'client_secret' => $config->getClientSecret(),
            'entry_point' => $config->getEntryPoint(),
            'log_level' => $config->getLogLevel(),
            'callback' => $config->getCallback(),
            'environment' => $config->getEnvironment(),
            'log_path' => $config->getLogPath(),
            'cache_path' => $config->getCachePath(),
            'domain' => $config->getDomain()
        );
        $formats = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

        if (empty($existing_config->getClientId())) {
            $query = $this->getWpdb()->insert(
                $this->getFullTableName(),
                $data,
                $formats
            );
        } else {
            $query = $this->getWpdb()->update(
                $this->getFullTableName(),
                $data,
                array(
                    'client_id' => $existing_config->getClientId()
                ),
                $formats,
                array('%s')
            );
        }

        if ($query === false) {
            throw new \Exception('Error update Config');
        }

        $cleanup_query = $this->getWpdb()->prepare(
            "DELETE FROM " . $this->getFullTableName() . " WHERE client_id <> %s",
            $config->getClientId()
        );
        $this->getWpdb()->query($cleanup_query);

        return true;
    }
}
