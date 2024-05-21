<?php

namespace WP_Druid\Services\Pulse;

use WP_Druid\Services\Errors as Errors_Service;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PulseRestClient
{
    private static $token_transient_name = 'pulse_access_token';
    private static $token_expiration_transient_name = 'pulse_access_token_expiration';

    // Función para obtener el token de acceso
    public static function getToken() {
        // Verificar si ya tenemos un token en la caché y si no ha expirado
        $token = get_transient(self::$token_transient_name);
        $expiration = get_transient(self::$token_expiration_transient_name);

        if ($token && $expiration && $expiration > time()) {
            return $token;
        }

        $client = new Client();

        $url = 'https://oauth2.ciam.demo.dru-id.com/oauth2/token';
        $data = [
            'client_id' => '582106697277461',
            'client_secret' => 'yvkFFBkOwOR8k0D1491TyaSvdsiaAh',
            'grant_type' => 'client_credentials'
        ];

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => $data,
            ]);

            // Decodificar la respuesta JSON
            $responseData = json_decode($response->getBody(), true);

            // Guardar el token en la caché
            set_transient(self::$token_transient_name, $responseData['access_token'], $responseData['expires_in']);
            // Guardar el tiempo de expiración del token en la caché
            set_transient(self::$token_expiration_transient_name, time() + $responseData['expires_in'], $responseData['expires_in']);

            return $responseData['access_token'];
        } catch (GuzzleException $e) {
            error_log('Pulse GetToken Exception:' . $e);
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);
            return null;
        }
    }

    // Función para enviar el objeto JSON al servicio web con el token de acceso
    public static function send($event) {
        $client = new Client();

        // Obtener el token de acceso
        $token = self::getToken();

        error_log('Pulse Token:' . $token);

        if (!$token) {
            return; // O manejar el error de alguna otra manera
        }

        $url = 'https://beats.pulse.demo.dru-id.com/identities';

        try {
            error_log('Pulse body:' . json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token, // Añadir el token al encabezado de autorización
                ],
                'json' => $event,
            ]);

            // Obtiene el cuerpo de la respuesta
            $body = $response->getBody();

            // Devuelve la respuesta como string
            return $body->getContents();
        } catch (GuzzleException $e) {
            error_log('Pulse sendEvent Exception:' . $e);
            Errors_Service::log_error(__CLASS__.' ('.__LINE__.')', $e);
        }
    }
}