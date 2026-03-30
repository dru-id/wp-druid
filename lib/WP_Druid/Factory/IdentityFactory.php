<?php namespace WP_Druid\Factory;

use Genetsis\core\OAuthConfig;
use Genetsis\Identity;
use WP_Druid\DAO\ConfigDAO;

class IdentityFactory {

    public static function init($sync = false)
    {
        $dao_config = new ConfigDAO();
        $wp_config = $dao_config->get();

        if (!self::hasValidConfiguration($wp_config)) {
            return false;
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

        return true;
    }

    public static function require_initialized($sync = false)
    {
        if (!self::init($sync)) {
            throw new \RuntimeException(__('DruID is not configured correctly.', WPDR_LANG_NS));
        }
    }

    private static function hasValidConfiguration($wp_config): bool
    {
        return $wp_config
            && !empty($wp_config->getClientId())
            && !empty($wp_config->getClientSecret())
            && !empty($wp_config->getEntryPoint())
            && !empty($wp_config->getEnvironment())
            && !empty($wp_config->getLogPath())
            && !empty($wp_config->getCachePath());
    }

    static function get_url_callback(): string
    {
        return home_url('/actions/callback');
    }

}
