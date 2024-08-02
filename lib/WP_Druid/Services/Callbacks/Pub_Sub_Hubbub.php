<?php namespace WP_Druid\Services\Callbacks;

use WP_Druid\Contracts\Callbacks\Callbackable as CallbackContract;

/**
 * This class manages all request received by PubSubHub service.
 *
 * @package WP Druid
 */
class Pub_Sub_Hubbub extends Callback_Base_Service implements CallbackContract
{
    public function run()
    {
        // TODO: check the source of this request.

        $payload = file_get_contents('php://input');
        if ($payload && ($payload = @json_decode($payload)) && ($payload instanceof \stdClass)) {
            do_action(WPDR_ACTION_PUBSUBHUBBUB, $payload);
        }

        exit();
    }
}
