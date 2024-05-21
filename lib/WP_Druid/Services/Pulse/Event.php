<?php namespace WP_Druid\Services\Pulse;

use JsonSerializable;
/**
 * @package WP Druid
 */
class Event implements JsonSerializable
{
    public $published;
    public $actor;
    public $verb;
    public $object;
    public $target;
    public $origin;
    public $provider;

    public function __construct($actorId, $objectUrl, $objectId, $objectDisplayName, $objectObjectType, $verb, $target)
    {
        $this->published = date('c');
        $this->actor = array(
            "externalIds" => array(
                "druid:objectid" => $actorId
            ),
            "objectType" => "person",
            "schemaOrg" => new \stdClass()
        );
        $this->verb = $verb;
        $this->object = array(
            "id" => $objectId,
            "url" => $objectUrl,
            "displayName" => $objectDisplayName,
            "objectType" => $objectObjectType
        );

        $this->target = $target;

        $this->origin = array(
            "_id" => "druid:ep:438896766239018-wogprod",
            "objectType" => "entrypoint",
            "displayName" => "Wog Prod Default",
            "actionApplication" => array(
                "_id" => "druid:app:438896766239018",
                "objectType" => "application",
                "displayName" => "Wog Prod"
            )
        );
        $this->provider = array(
            "id" => "wordpress",
            "objectType" => "application",
            "displayName" => "Wordpress"
        );
    }

    public function jsonSerialize()
    {
        $json = get_object_vars($this);

        // Remove null target if it exists
        if (is_null($json['target'])) {
            unset($json['target']);
        }

        return $json;
    }
}
