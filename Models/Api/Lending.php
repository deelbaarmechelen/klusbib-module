<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;

class Lending extends BaseModel
{
    protected $endpoint = 'lendings';
    protected $primaryKey = 'lending_id';

    /**
     * Execute the query and get the first result.
     *
     * @param  string $id
     * @param  array  $params
     * @return mixed|static
     */
    public static function findActiveByUserTool($userId, $toolId, $toolType)
    {
        $instance = new static([], static::getParentID());

        return $instance->request($instance->getEndpoint(), 'findActiveByUserTool', [$userId, $toolId, $toolType]);
    }


}