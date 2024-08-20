<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5dd35187e4fcaaf63de4a4ec0842665e
{
    public static $files = array (
        '7b11c4dc42b3b3023073cb14e519683c' => __DIR__ . '/..' . '/ralouphie/getallheaders/src/getallheaders.php',
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '37a3dc5111fe8f707ab4c132ef1dbc62' => __DIR__ . '/..' . '/guzzlehttp/guzzle/src/functions_include.php',
        '593c6a09ddf670156f174aaa02145c2b' => __DIR__ . '/../..' . '/lib/WP_Druid/Utils/helpers.php',
        'a9789c050b102af5d1c182240a406ddf' => __DIR__ . '/../..' . '/lib/WP_Druid/Front/Collections/actions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
            'PhpConsole\\' => 11,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
        'G' => 
        array (
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
            'GuzzleHttp\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Psr\\Http\\Message\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-factory/src',
            1 => __DIR__ . '/..' . '/psr/http-message/src',
        ),
        'Psr\\Http\\Client\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/http-client/src',
        ),
        'PhpConsole\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-console/php-console/src/PhpConsole',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
        'GuzzleHttp\\Psr7\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/psr7/src',
        ),
        'GuzzleHttp\\Promise\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/promises/src',
        ),
        'GuzzleHttp\\' => 
        array (
            0 => __DIR__ . '/..' . '/guzzlehttp/guzzle/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'Hashids' => 
            array (
                0 => __DIR__ . '/..' . '/hashids/hashids/lib',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Genetsis\\AutoloaderClass' => __DIR__ . '/../..' . '/lib/php-sdk/src/Autoloader.php',
        'Genetsis\\Identity' => __DIR__ . '/../..' . '/lib/php-sdk/src/Identity.php',
        'Genetsis\\URLBuilder' => __DIR__ . '/../..' . '/lib/php-sdk/src/URLBuilder.php',
        'Genetsis\\UserApi' => __DIR__ . '/../..' . '/lib/php-sdk/src/UserApi.php',
        'Genetsis\\core\\AccessToken' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/AccessToken.php',
        'Genetsis\\core\\ClientToken' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/ClientToken.php',
        'Genetsis\\core\\Encryption' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/Encryption.php',
        'Genetsis\\core\\FileCache' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/FileCache.php',
        'Genetsis\\core\\InvalidGrantException' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/InvalidGrantException.php',
        'Genetsis\\core\\LogConfig' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/LogConfig.php',
        'Genetsis\\core\\LoginStatus' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/LoginStatus.php',
        'Genetsis\\core\\LoginStatusType' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/LoginStatusType.php',
        'Genetsis\\core\\OAuth' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/OAuth.php',
        'Genetsis\\core\\OAuthConfig' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/OAuthConfig.php',
        'Genetsis\\core\\OauthTemplate' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/OauthTemplate.php',
        'Genetsis\\core\\RefreshToken' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/RefreshToken.php',
        'Genetsis\\core\\Request' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/Request.php',
        'Genetsis\\core\\StoredToken' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/StoredToken.php',
        'Genetsis\\core\\Things' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/Things.php',
        'Genetsis\\core\\iTokenTypes' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/iTokenTypes.php',
        'Logger' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/Logger.php',
        'LoggerAppender' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerAppender.php',
        'LoggerAppenderConsole' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderConsole.php',
        'LoggerAppenderDailyFile' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderDailyFile.php',
        'LoggerAppenderDailyRollingFile' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderDailyRollingFile.php',
        'LoggerAppenderEcho' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderEcho.php',
        'LoggerAppenderFile' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderFile.php',
        'LoggerAppenderFirePHP' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderFirePHP.php',
        'LoggerAppenderMail' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderMail.php',
        'LoggerAppenderMailEvent' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderMailEvent.php',
        'LoggerAppenderMongoDB' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderMongoDB.php',
        'LoggerAppenderNull' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderNull.php',
        'LoggerAppenderPDO' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderPDO.php',
        'LoggerAppenderPhp' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderPhp.php',
        'LoggerAppenderPool' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerAppenderPool.php',
        'LoggerAppenderRollingFile' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderRollingFile.php',
        'LoggerAppenderSocket' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderSocket.php',
        'LoggerAppenderSyslog' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/appenders/LoggerAppenderSyslog.php',
        'LoggerAutoloader' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerAutoloader.php',
        'LoggerConfigurable' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerConfigurable.php',
        'LoggerConfigurationAdapter' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/configurators/LoggerConfigurationAdapter.php',
        'LoggerConfigurationAdapterINI' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/configurators/LoggerConfigurationAdapterINI.php',
        'LoggerConfigurationAdapterPHP' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/configurators/LoggerConfigurationAdapterPHP.php',
        'LoggerConfigurationAdapterXML' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/configurators/LoggerConfigurationAdapterXML.php',
        'LoggerConfigurator' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerConfigurator.php',
        'LoggerConfiguratorDefault' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/configurators/LoggerConfiguratorDefault.php',
        'LoggerException' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerException.php',
        'LoggerFilter' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerFilter.php',
        'LoggerFilterDenyAll' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/filters/LoggerFilterDenyAll.php',
        'LoggerFilterLevelMatch' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/filters/LoggerFilterLevelMatch.php',
        'LoggerFilterLevelRange' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/filters/LoggerFilterLevelRange.php',
        'LoggerFilterStringMatch' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/filters/LoggerFilterStringMatch.php',
        'LoggerFormattingInfo' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/helpers/LoggerFormattingInfo.php',
        'LoggerHierarchy' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerHierarchy.php',
        'LoggerLayout' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerLayout.php',
        'LoggerLayoutHtml' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/layouts/LoggerLayoutHtml.php',
        'LoggerLayoutPattern' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/layouts/LoggerLayoutPattern.php',
        'LoggerLayoutSerialized' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/layouts/LoggerLayoutSerialized.php',
        'LoggerLayoutSimple' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/layouts/LoggerLayoutSimple.php',
        'LoggerLayoutTTCC' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/layouts/LoggerLayoutTTCC.php',
        'LoggerLayoutXml' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/layouts/LoggerLayoutXml.php',
        'LoggerLevel' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerLevel.php',
        'LoggerLocationInfo' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerLocationInfo.php',
        'LoggerLoggingEvent' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerLoggingEvent.php',
        'LoggerMDC' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerMDC.php',
        'LoggerNDC' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerNDC.php',
        'LoggerOptionConverter' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/helpers/LoggerOptionConverter.php',
        'LoggerPatternConverter' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverter.php',
        'LoggerPatternConverterClass' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterClass.php',
        'LoggerPatternConverterCookie' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterCookie.php',
        'LoggerPatternConverterDate' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterDate.php',
        'LoggerPatternConverterEnvironment' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterEnvironment.php',
        'LoggerPatternConverterFile' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterFile.php',
        'LoggerPatternConverterLevel' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterLevel.php',
        'LoggerPatternConverterLine' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterLine.php',
        'LoggerPatternConverterLiteral' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterLiteral.php',
        'LoggerPatternConverterLocation' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterLocation.php',
        'LoggerPatternConverterLogger' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterLogger.php',
        'LoggerPatternConverterMDC' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterMDC.php',
        'LoggerPatternConverterMessage' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterMessage.php',
        'LoggerPatternConverterMethod' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterMethod.php',
        'LoggerPatternConverterNDC' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterNDC.php',
        'LoggerPatternConverterNewLine' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterNewLine.php',
        'LoggerPatternConverterProcess' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterProcess.php',
        'LoggerPatternConverterRelative' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterRelative.php',
        'LoggerPatternConverterRequest' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterRequest.php',
        'LoggerPatternConverterServer' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterServer.php',
        'LoggerPatternConverterSession' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterSession.php',
        'LoggerPatternConverterSessionID' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterSessionID.php',
        'LoggerPatternConverterSuperglobal' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterSuperglobal.php',
        'LoggerPatternConverterThrowable' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/pattern/LoggerPatternConverterThrowable.php',
        'LoggerPatternParser' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/helpers/LoggerPatternParser.php',
        'LoggerReflectionUtils' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerReflectionUtils.php',
        'LoggerRenderer' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/renderers/LoggerRenderer.php',
        'LoggerRendererDefault' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/renderers/LoggerRendererDefault.php',
        'LoggerRendererException' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/renderers/LoggerRendererException.php',
        'LoggerRendererMap' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/renderers/LoggerRendererMap.php',
        'LoggerRoot' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerRoot.php',
        'LoggerThrowableInformation' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/LoggerThrowableInformation.php',
        'LoggerUtils' => __DIR__ . '/../..' . '/lib/php-sdk/src/core/log4php/helpers/LoggerUtils.php',
        'WP_Druid\\Admin\\Controllers\\Admin_Controller' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Controllers/Admin_Controller.php',
        'WP_Druid\\Admin\\Controllers\\Error_Log' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Controllers/Error_Log.php',
        'WP_Druid\\Admin\\Controllers\\Home' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Controllers/Home.php',
        'WP_Druid\\Admin\\Controllers\\Promotions' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Controllers/Promotions.php',
        'WP_Druid\\Admin\\Controllers\\Rewrites' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Controllers/Rewrites.php',
        'WP_Druid\\Admin\\Router' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Router.php',
        'WP_Druid\\Admin\\Services\\Admin_Menu_Manager' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Services/Admin_Menu_Manager.php',
        'WP_Druid\\Admin\\Services\\Admin_Messages' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Services/Admin_Messages.php',
        'WP_Druid\\Admin\\Services\\Promotion_List' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/Services/Promotion_List.php',
        'WP_Druid\\Admin\\WP_Druid_Admin' => __DIR__ . '/../..' . '/lib/WP_Druid/Admin/WP_Druid_Admin.php',
        'WP_Druid\\Contracts\\Callbacks\\Callbackable' => __DIR__ . '/../..' . '/lib/WP_Druid/Contracts/Callbacks/Callback.php',
        'WP_Druid\\DAO\\ConfigDAO' => __DIR__ . '/../..' . '/lib/WP_Druid/DAO/ConfigDAO.php',
        'WP_Druid\\DAO\\DAO' => __DIR__ . '/../..' . '/lib/WP_Druid/DAO/DAO.php',
        'WP_Druid\\Exceptions\\Callbacks\\Callback_Exception' => __DIR__ . '/../..' . '/lib/WP_Druid/Exceptions/Callbacks/Callback_Exception.php',
        'WP_Druid\\Exceptions\\Users\\Create_User_Exception' => __DIR__ . '/../..' . '/lib/WP_Druid/Exceptions/Users/Create_User_Exception.php',
        'WP_Druid\\Exceptions\\Users\\Login_User_Exception' => __DIR__ . '/../..' . '/lib/WP_Druid/Exceptions/Users/Login_User_Exception.php',
        'WP_Druid\\Factory\\IdentityFactory' => __DIR__ . '/../..' . '/lib/WP_Druid/Factory/IdentityFactory.php',
        'WP_Druid\\Front\\Collections\\Callbacks\\Post_Login_Parameters' => __DIR__ . '/../..' . '/lib/WP_Druid/Front/Collections/Callbacks/Post_Login_Parameters.php',
        'WP_Druid\\Front\\Collections\\Router\\Router_Parameters' => __DIR__ . '/../..' . '/lib/WP_Druid/Front/Collections/Router/Router_Parameters.php',
        'WP_Druid\\Front\\Router' => __DIR__ . '/../..' . '/lib/WP_Druid/Front/Router.php',
        'WP_Druid\\Front\\WP_Druid_Public' => __DIR__ . '/../..' . '/lib/WP_Druid/Front/WP_Druid_Public.php',
        'WP_Druid\\Models\\Config' => __DIR__ . '/../..' . '/lib/WP_Druid/Models/Config.php',
        'WP_Druid\\Services\\Callbacks\\Callback_Base_Service' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Callbacks/Callback_Base_Service.php',
        'WP_Druid\\Services\\Callbacks\\Logout' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Callbacks/Logout.php',
        'WP_Druid\\Services\\Callbacks\\Post_Login' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Callbacks/Post_Login.php',
        'WP_Druid\\Services\\Callbacks\\Pub_Sub_Hubbub' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Callbacks/Pub_Sub_Hubbub.php',
        'WP_Druid\\Services\\DB' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/DB.php',
        'WP_Druid\\Services\\Errors' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Errors.php',
        'WP_Druid\\Services\\Installer' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Installer.php',
        'WP_Druid\\Services\\Render' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Render.php',
        'WP_Druid\\Services\\Shortcodes' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Shortcodes.php',
        'WP_Druid\\Services\\Users' => __DIR__ . '/../..' . '/lib/WP_Druid/Services/Users.php',
        'WP_Druid\\Utils\\Session\\Services\\SessionManager' => __DIR__ . '/../..' . '/lib/WP_Druid/Utils/Session/Services/SessionManager.php',
        'WP_Druid\\Utils\\Wp\\Services\\Query_Vars' => __DIR__ . '/../..' . '/lib/WP_Druid/Utils/Wp/Services/Query_Vars.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5dd35187e4fcaaf63de4a4ec0842665e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5dd35187e4fcaaf63de4a4ec0842665e::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit5dd35187e4fcaaf63de4a4ec0842665e::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit5dd35187e4fcaaf63de4a4ec0842665e::$classMap;

        }, null, ClassLoader::class);
    }
}
