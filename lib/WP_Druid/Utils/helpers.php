<?php

use WP_Druid\Utils\Session\Services\SessionManager;
use Genetsis\Identity;
use Genetsis\URLBuilder;

if (!defined('WPDR_FUNCTIONS_HELPERS')) {
    define('WPDR_FUNCTIONS_HELPERS', true);

    /**
     * This function allows you to use the PHP 5.4 syntax for temporal objects: (new Object())
     *
     * Usage:
     * druid_x(new YOUR_OBJECT)->YOUR_OBJECT_METHOD()
     *
     * Note that this function does not validate anything, it just returns the instantiated object to be directly used.
     *
     * @param mixed $object
     * @return mixed The $object itself.
     */
    function druid_x ($object)
    {
        return $object;
    }

    /**
     * @param string $default_client
     * @return string
     * @todo This function won't be used when data were stored in database.
     */
    function druid_get_current_client ($default_client = 'default')
    {
        return $default_client;
    }

    /**
     * Indicates if there is a DruID user and it is connected.
     *
     * @return boolean
     */
    function druid_is_user_connected()
    {
        return Identity::isConnected();
    }

    /**
     * Checks if the user should complete its data.
     *
     * @param null $scope
     * @return boolean TRUE if there are data to be fulfilled or FALSE if everything is OK.
     * @throws Exception
     */
    function druid_should_user_completed_account($scope = null)
    {
        return !Identity::checkUserComplete($scope);
    }

    /**
     * Checks if the user need to accept terms and conditions.
     *
     * @param string $scope
     * @return boolean TRUE if the user need to accept terms and conditions or FALSE if it has already accepted.
     * @throws Exception
     */
    function druid_should_user_accept_terms_and_conditions($scope)
    {
        return Identity::checkUserNeedAcceptTerms($scope);
    }

    /**
     * Returns the login URL.
     *
     * @param string|null $scope
     * @param string|null $social
     * @param string|null $callback_url
     * @return string
     */
    function druid_get_login_url($scope = null, $social = null, $callback_url = null)
    {
        SessionManager::set(WPDR_CUSTOM_RETURN_URL_SESSION_KEY, $callback_url);
        return URLBuilder::getUrlLogin($scope, $social);
    }

    /**
     * Returns the registration URL
     *
     * @param string|null $scope
     * @param string|null $callback_url
     * @return string
     */
    function druid_get_register_url($scope = null, $callback_url = null)
    {
        SessionManager::set(WPDR_CUSTOM_RETURN_URL_SESSION_KEY, $callback_url);
        return URLBuilder::getUrlRegister($scope);
    }

    /**
     * Returns the complete account URL.
     *
     * @param string|null $scope
     * @return string
     */
    function druid_get_complete_account($scope = null)
    {
        return URLBuilder::getUrlCompleteAccount($scope);
    }

    /**
     * Returns the edit account URL.
     *
     * @param string|null $scope
     * @param string|null $callback_url
     * @return string
     */
    function druid_get_edit_account($scope = null, $callback_url = null)
    {
        SessionManager::set(WPDR_CUSTOM_RETURN_URL_SESSION_KEY, $callback_url);
        return URLBuilder::getUrlEditAccount($scope);
    }

    /**
     * Checks if an URL is valid or not.
     *
     * @param string $url
     * @return boolean
     */
    function druid_validate_url($url)
    {
        /*
         * This pattern is derived from Symfony\Component\Validator\Constraints\UrlValidator (2.7.4)
         * (c) Fabien Potencier <fabien@symfony.com> http://symfony.com
         */
        $pattern = '~^
            ((aaa|aaas|about|acap|acct|acr|adiumxtra|afp|afs|aim|apt|attachment|aw|barion|beshare|bitcoin|blob|bolo|callto|cap|chrome|chrome-extension|cid|coap|coaps|com-eventbrite-attendee|content|crid|cvs|data|dav|dict|dlna-playcontainer|dlna-playsingle|dns|dntp|dtn|dvb|ed2k|example|facetime|fax|feed|feedready|file|filesystem|finger|fish|ftp|geo|gg|git|gizmoproject|go|gopher|gtalk|h323|ham|hcp|http|https|iax|icap|icon|im|imap|info|iotdisco|ipn|ipp|ipps|irc|irc6|ircs|iris|iris.beep|iris.lwz|iris.xpc|iris.xpcs|itms|jabber|jar|jms|keyparc|lastfm|ldap|ldaps|magnet|mailserver|mailto|maps|market|message|mid|mms|modem|ms-help|ms-settings|ms-settings-airplanemode|ms-settings-bluetooth|ms-settings-camera|ms-settings-cellular|ms-settings-cloudstorage|ms-settings-emailandaccounts|ms-settings-language|ms-settings-location|ms-settings-lock|ms-settings-nfctransactions|ms-settings-notifications|ms-settings-power|ms-settings-privacy|ms-settings-proximity|ms-settings-screenrotation|ms-settings-wifi|ms-settings-workplace|msnim|msrp|msrps|mtqp|mumble|mupdate|mvn|news|nfs|ni|nih|nntp|notes|oid|opaquelocktoken|pack|palm|paparazzi|pkcs11|platform|pop|pres|prospero|proxy|psyc|query|redis|rediss|reload|res|resource|rmi|rsync|rtmfp|rtmp|rtsp|rtsps|rtspu|secondlife|service|session|sftp|sgn|shttp|sieve|sip|sips|skype|smb|sms|smtp|snews|snmp|soap.beep|soap.beeps|soldat|spotify|ssh|steam|stun|stuns|submit|svn|tag|teamspeak|tel|teliaeid|telnet|tftp|things|thismessage|tip|tn3270|turn|turns|tv|udp|unreal|urn|ut2004|vemmi|ventrilo|videotex|view-source|wais|webcal|ws|wss|wtai|wyciwyg|xcon|xcon-userid|xfire|xmlrpc\.beep|xmlrpc.beeps|xmpp|xri|ymsgr|z39\.50|z39\.50r|z39\.50s))://                                 # protocol
            (([\pL\pN-]+:)?([\pL\pN-]+)@)?          # basic auth
            (
                ([\pL\pN\pS-\.])+(\.?([\pL]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                    |                                              # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                 # a IP address
                    |                                              # or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # a IPv6 address
            )
            (:[0-9]+)?                              # a port (optional)
            (/?|/\S+)                               # a /, nothing or a / with something
        $~ixu';

        return preg_match($pattern, $url) === 1;
    }

}