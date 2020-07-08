<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Klusbib\Models\Api\Stat;

class StatsController extends Controller
{

    public function getUsersCountByProject()
    {
        $monthlyStats = Stat::monthly();
//        \Log::debug('Klusbib Dashboard: monthly stats=' . var_dump($monthlyStats));
        $counts['user'] = isset($monthlyStats["user"]) ? $monthlyStats["user"]["total-count"] : "0";
        $counts['user_stroom'] = isset($monthlyStats["user"]) && isset($monthlyStats["user"]["stroom"]) ? $monthlyStats["user"]["stroom"]["total-count"] : "0";

        $stroomCount = $counts['user_stroom'];
        $othersCount = $counts['user'] - $counts['user_stroom'];
        $userProjects = array( "Stroom" => $stroomCount, "Anderen" => $othersCount);
        $labels=[];
        $points=[];
        $colors=[];
        foreach ($userProjects as $projectName => $count) {
            if ($count > 0) {

                $labels[]=$projectName. ' ('.number_format($count).')';
                $points[]=$count;
//                if ($statuslabel->color!='') {
//                    $colors[]=$statuslabel->color;
//                }
            }
        }


        $colors_array = array_merge($colors, Helper::chartColors());

        $result= [
            "labels" => $labels,
            "datasets" => [ [
                "data" => $points,
                "backgroundColor" => $colors_array,
                "hoverBackgroundColor" =>  $colors_array
            ]]
        ];
        return $result;
    }

    public function getActivityCountByProject()
    {
        $monthlyStats = Stat::monthly();
//        \Log::debug('Klusbib Dashboard: monthly stats=' . var_dump($monthlyStats));
        $counts['activity'] = isset($monthlyStats["activity"]) ? $monthlyStats["activity"]["total-count"] : "0";
        $counts['activity_stroom'] = isset($monthlyStats["activity"]) && isset($monthlyStats["activity"]["stroom"]) ? $monthlyStats["activity"]["stroom"]["total-count"] : "0";

        $stroomCount = $counts['activity_stroom'];
        $othersCount = $counts['activity'] - $counts['activity_stroom'];
        $userProjects = array( "Stroom" => $stroomCount, "Anderen" => $othersCount);
        $labels=[];
        $points=[];
        $colors=[];
        foreach ($userProjects as $projectName => $count) {
            if ($count > 0) {

                $labels[]=$projectName. ' ('.number_format($count).')';
                $points[]=$count;
//                if ($statuslabel->color!='') {
//                    $colors[]=$statuslabel->color;
//                }
            }
        }


        $colors_array = array_merge($colors, Helper::chartColors());

        $result= [
            "labels" => $labels,
            "datasets" => [ [
                "data" => $points,
                "backgroundColor" => $colors_array,
                "hoverBackgroundColor" =>  $colors_array
            ]]
        ];
        return $result;
    }

 }
