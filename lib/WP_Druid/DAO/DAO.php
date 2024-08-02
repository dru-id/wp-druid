<?php namespace WP_Druid\DAO;

class DAO
{

    /**
     * @var string $tableName
     */
    private $fullTableName;

    /**
     * @var \wpdb $wpdb
     */
    private $wpdb;

    /**
     * DAO constructor.
     */
    public function __construct() {
        global $wpdb;
        $this->setWpdb($wpdb);
    }

    /**
     * @return string
     */
    public function getFullTableName(): string
    {
        return $this->fullTableName;
    }

    /**
     * @param string $fullTableName
     */
    public function setFullTableName(string $fullTableName)
    {
        $this->fullTableName = $this->wpdb->prefix . $fullTableName;
    }

    /**
     * @return \wpdb
     */
    public function getWpdb()
    {
        return $this->wpdb;
    }

    /**
     * @param \wpdb $wpdb
     */
    public function setWpdb( $wpdb)
    {
        $this->wpdb = $wpdb;
    }


}