<?php namespace WP_Druid\Services\Pulse;

use WP_Druid\Services\Pulse\PulseRestClient as Pulse_Rest_Client;

/**
 * @package WP Druid
 */
class EventService
{

    public static function send_event($userId, $source, $id = null, $current_url = null, $url = null, $text = null): void
    {
        switch ($source) {
            case 'click':
                $objectDisplayName = 'BTN ' . $text;
                $target = array(
                    "_id" => $id,
                    "objectType" => "page",
                    "displayName" => $text,
                    "url" => $current_url . $url
                );
                $evento = new Event($userId, $current_url, $id, $objectDisplayName, "link", "click", $target);
                Pulse_Rest_Client::send($evento);
                break;

            case 'promotion':
                $evento = new Event($userId, $url, $id, $text, "promotion", "attend", null);
                Pulse_Rest_Client::send($evento);
                break;

            case 'view':

                if (in_array(get_the_ID(), array(45, 278, 180))) {
                    $objectUrl = get_permalink();
                    $objectId = get_the_ID();
                    $objectDisplayName = get_the_title();
                    $evento = new Event($userId, $objectUrl, $objectId, $objectDisplayName, "page", "watch", null);
                    Pulse_Rest_Client::send($evento);
                }
                break;

            default:
                // Manejo de otros casos o un caso por defecto si es necesario
                error_log("Fuente no manejada: " . $source);
                break;
        }
    }
}
