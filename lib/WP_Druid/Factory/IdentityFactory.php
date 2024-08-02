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
                ->setCallback($wp_config->getCallback())
                ->setEntryPoints(array($wp_config->getEntryPoint()))
                ->setCachePath($wp_config->getCachePath())
                ->setLogPath($wp_config->getLogPath())
                ->setLogLevel(strtoupper($wp_config->getLogLevel()))
                ->setDomain($wp_config->getDomain());

            Identity::init($druid_config, $sync);

        } catch (\Exception $e) {
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);
        }
    }
}