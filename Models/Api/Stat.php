<?php
namespace Modules\Klusbib\Models\Api;

use Torann\RemoteModel\Model as BaseModel;
use DateTimeImmutable;

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

        $now = new DateTimeImmutable("now");
        $prevMonth = $now->sub(new \DateInterval('P1M'));
        $statsPrevMonth = $instance->request($instance->getEndpoint(), 'monthly', ['version' => 2, 'statMonth' => $prevMonth->format('Y-m')]);
        $prevUserStat = \json_decode($statsPrevMonth["user-statistics"], true);
        $prevActivityStat = \json_decode($statsPrevMonth["activity-statistics"], true);
        $prevMembershipStats = \json_decode($statsPrevMonth["membership-statistics"], true);

        // Calling monthly method on stats endpoint with parameter version set to 2
        $stats = $instance->request($instance->getEndpoint(), 'monthly', ['version' => 2]);
        $membershipStats = \json_decode($stats["membership-statistics"], true);
        $userStats = \json_decode($stats["user-statistics"], true);
        $toolStats = \json_decode($stats["tool-statistics"], true);
        $activityStats = \json_decode($stats["activity-statistics"], true);
        return array (
            "user" => $userStats,
            "tool" => $toolStats,
            "activity" => $activityStats,
            "membership" => $membershipStats,
            "prevMonth" => [
                "membership" => $prevMembershipStats,
                "user" => $prevUserStat,
                "activity" => $prevActivityStat,    
            ]
        );
    }

}