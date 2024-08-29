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
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists() {
        $query = "
            CREATE TABLE IF NOT EXISTS " . $this->getFullTableName() . " (
                client_id VARCHAR(255) NOT NULL PRIMARY KEY,
                client_secret VARCHAR(255) NOT NULL,
                entry_point VARCHAR(255),
                log_level VARCHAR(50),
                callback VARCHAR(255),
                environment VARCHAR(50),
                log_path VARCHAR(255),
                cache_path VARCHAR(255),
                domain VARCHAR(255)
            )";

        $this->getWpdb()->query($query);
    }

    public function get() {
        $result = $this->getWpdb()->get_row("SELECT * FROM " . $this->getFullTableName());

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

        if (empty($this->getByClientID($config->getClientId())->getClientId())) {
            $query = $this->getWpdb()->insert(
                $this->getFullTableName(),
                array(
                    'client_id' => $config->getClientId(),
                    'client_secret' => $config->getClientSecret(),
                    'entry_point' => $config->getEntryPoint(),
                    'log_level' => $config->getLogLevel(),
                    'callback' => $config->getCallback(),
                    'environment' => $config->getEnvironment(),
                    'log_path' => $config->getLogPath(),
                    'cache_path' => $config->getCachePath(),
                    'domain' => $config->getDomain()
                )
            );
        } else {
            $query = $this->getWpdb()->update(
                $this->getFullTableName(),
                array(
                    'client_id' => $config->getClientId(),
                    'client_secret' => $config->getClientSecret(),
                    'entry_point' => $config->getEntryPoint(),
                    'log_level' => $config->getLogLevel(),
                    'callback' => $config->getCallback(),
                    'environment' => $config->getEnvironment(),
                    'log_path' => $config->getLogPath(),
                    'cache_path' => $config->getCachePath(),
                    'domain' => $config->getDomain()
                ),
                array(
                    'client_id' => $config->getClientId()
                )
            );
        }

        if ($query === false) {
            throw new \Exception('Error update Config');
        }

        return true;
    }
}
