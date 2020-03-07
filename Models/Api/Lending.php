<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;

class Lending extends BaseModel
{
    protected $endpoint = 'lendings';
    protected $primaryKey = 'lending_id';

    /**
     * Lookup an active lending for given user id, tool id, tool type.
     *
     * @param  string $userId
     * @param  string $toolId
     * @param  string $toolType
     * @return mixed|static the lending or null if no lending exist
     */
    public static function findActiveByUserTool($userId, $toolId, $toolType)
    {
        $instance = new static([], static::getParentID());

        return $instance->request($instance->getEndpoint(), 'findActiveByUserTool', [$userId, $toolId, $toolType]);
    }

    /**
     * Lookup a lending for given user id, tool id, tool type and start date.
     *
     * @param  string $userId
     * @param  string $toolId
     * @param  string $toolType
     * @param  string $startDate
     * @return mixed|static the lending or null if no lending exist
     */
    public static function findByUserToolStart($userId, $toolId, $toolType, $startDate)
    {
        $instance = new static([], static::getParentID());

        return $instance->request($instance->getEndpoint(), 'findByUserToolStart', [$userId, $toolId, $toolType, $startDate]);
    }

}