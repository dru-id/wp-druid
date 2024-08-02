<?php namespace WP_Druid\Models;

class Config
{
    /**
     * @var String
     */
    protected $client_id = '';

    /**
     * @var String
     */
    protected $client_secret = '';

    /**
     * @var String
     */
    protected $entry_point = '';

    /**
     * @var String
     */
    protected $log_level = 'DEBUG';

    /**
     * @var String
     */
    protected $callback = '';

    /**
     * ../wp-druid-files/runtime/logs/
     * @var string
     */
    protected $log_path = '';

    /**
     * ../wp-druid-files/runtime/cache/
     * @var string
     */
    protected $cache_path = '';

    protected $environment = 'dev';

    /**
     * @var String
     */
    protected $domain = '';

    public function __construct()
    {
    }

    /**
     * @return String
     */
    public function getClientId(): String
    {
        return $this->client_id;
    }

    /**
     * @param String $client_id
     * @return Config
     */
    public function setClientId(String $client_id): Config
    {
        $this->client_id = $client_id;
        return $this;
    }

    /**
     * @return String
     */
    public function getClientSecret(): String
    {
        return $this->client_secret;
    }

    /**
     * @param String $client_secret
     * @return Config
     */
    public function setClientSecret(String $client_secret): Config
    {
        $this->client_secret = $client_secret;
        return $this;
    }

    /**
     * @return String
     */
    public function getEntryPoint(): String
    {
        return $this->entry_point;
    }

    /**
     * @param String $entry_point
     * @return Config
     */
    public function setEntryPoint(String $entry_point): Config
    {
        $this->entry_point = $entry_point;
        return $this;
    }

    /**
     * @return String
     */
    public function getLogLevel(): String
    {
        return $this->log_level;
    }

    /**
     * @param String $log_level
     * @return Config
     */
    public function setLogLevel(String $log_level): Config
    {
        $this->log_level = $log_level;
        return $this;
    }

    /**
     * @return String
     */
    public function getCallback(): String
    {
        return $this->callback;
    }

    /**
     * @param String $callback
     * @return Config
     */
    public function setCallback(String $callback): Config
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLogPath()
    {
        return $this->log_path;
    }

    /**
     * @param mixed $log_path
     * @return Config
     */
    public function setLogPath($log_path)
    {
        $this->log_path = $log_path;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCachePath()
    {
        return $this->cache_path;
    }

    /**
     * @param mixed $cache_path
     * @return Config
     */
    public function setCachePath($cache_path)
    {
        $this->cache_path = $cache_path;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param mixed $environment
     * @return Config
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * @return String
     */
    public function getDomain(): String
    {
        return $this->domain;
    }

    /**
     * @param String $domain
     * @return Config
     */
    public function setDomain(String $domain): Config
    {
        $this->domain = $domain;
        return $this;
    }
}
