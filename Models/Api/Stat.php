<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;

class Stat extends BaseModel
{
    protected $endpoint = 'stats';

    /**
     * Lookup a monthly statistics.
     *
     * @return mixed|static the statistics of current month
     */
    public static function monthly()
    {
        $instance = new static([], static::getParentID());


        $stats = $instance->request($instance->getEndpoint(), 'monthly', []);
        $userStats = \json_decode($stats["user-statistics"], true);
        $toolStats = \json_decode($stats["tool-statistics"], true);
        $accessoryStats = \json_decode($stats["accessory-statistics"], true);
        $activityStats = \json_decode($stats["activity-statistics"], true);
        return array ("user" => $userStats, "tool" => $toolStats, "accessory" => $accessoryStats, "activity" => $activityStats);
    }

}