<?php namespace WP_Druid\Factory;

use Genetsis\core\OAuthConfig;
use Genetsis\DruidConfig;
use Genetsis\Identity;
use WP_Druid\DAO\ConfigDAO;
use WP_Druid\Services\Errors as Errors_Service;

class IdentityFactory {

    public static function init($sync = false) {
        try {
            $dao_config = new ConfigDAO();
            $wp_config = $dao_config->get();

            if ($wp_config == null) {
                throw new \Exception('Invalid Druid Configuration');
            }
            $druid_config = OAuthConfig::init()->setClientId($wp_config->getClientId())
                ->setClientSecret($wp_config->getClientSecret())
                ->setEnvironment(strtolower($wp_config->getEnvironment()))
                ->setEntryPoints(array($wp_config->getEntryPoint()))
                ->setCallback($wp_config->getCallback() ?: self::get_url_callback())
                ->setCachePath($wp_config->getCachePath())
                ->setLogPath($wp_config->getLogPath())
                ->setLogLevel(strtoupper($wp_config->getLogLevel()))
                ->setDomain($wp_config->getDomain());

            Identity::init($druid_config, $sync);

        } catch (\Exception $e) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);
        }
    }

    static function get_url_callback(): string
    {
        $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'];
        return $protocolo . $domain . '/actions/callback';
    }

}