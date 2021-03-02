<?php namespace Utils\Console_Log\Loggers;

use Monolog\Logger;
use Monolog\Handler\ChromePHPHandler;

/**
 * This library allows you to send data to user's browser console.
 *
 * This implementation uses the ChromePHP library, which has have more properties
 * than other loggers. For example, it is able to send arrays with the output format
 * used with print_r or var_export functions.
 *
 * @see http://craig.is/writing/chrome-logger
 */
class Chrome_Console_Logger
{
    /** @var \Monolog\Logger $logger */
    private static $logger = null;

    private static function init()
    {
        if (!(self::$logger instanceof Logger)) {
            self::$logger = new Logger('[DRUID] ');
            self::$logger->pushHandler(new ChromePHPHandler());
        }
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function debug($message, array $context = array())
    {
        self::init();
        return self::$logger->debug($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function info($message, array $context = array())
    {
        self::init();
        return self::$logger->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function notice($message, array $context = array())
    {
        self::init();
        return self::$logger->notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function warn($message, array $context = array())
    {
        self::init();
        return self::$logger->warn($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function warning($message, array $context = array())
    {
        self::init();
        return self::$logger->warning($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function err($message, array $context = array())
    {
        self::init();
        return self::$logger->err($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function error($message, array $context = array())
    {
        self::init();
        return self::$logger->error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function crit($message, array $context = array())
    {
        self::init();
        return self::$logger->crit($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function critical($message, array $context = array())
    {
        self::init();
        return self::$logger->critical($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function alert($message, array $context = array())
    {
        self::init();
        return self::$logger->alert($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function emerg($message, array $context = array())
    {
        self::init();
        return self::$logger->emerg($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     * @return string
     */
    public static function emergency($message, array $context = array())
    {
        self::init();
        return self::$logger->emergency($message, $context);
    }
}