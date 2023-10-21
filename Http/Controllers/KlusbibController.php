<?php

namespace Modules\Klusbib\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Klusbib\Models\Api\Stat;

class KlusbibController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $monthlyStats = Stat::monthly();
    //    \Log::info('Klusbib Dashboard: monthly stats=' . var_dump($monthlyStats));
        $counts['user'] = isset($monthlyStats["user"]) ? $monthlyStats["user"]["total-count"] : "0";
        $counts['user_active'] = isset($monthlyStats["user"]) ? $monthlyStats["user"]["active-count"] : "0";
        $counts['user_expired'] = isset($monthlyStats["user"]) ? $monthlyStats["user"]["expired-count"] : "0";
        $counts['user_deleted'] = isset($monthlyStats["user"]) ? $monthlyStats["user"]["deleted-count"] : "0";
        $counts['asset'] = isset($monthlyStats["tool"]) ? $monthlyStats["tool"]["total-count"] : "0";
        $counts['accessory'] = isset($monthlyStats["accessory"]) ? $monthlyStats["accessory"]["total-count"] : \App\Models\Accessory::count();
        $counts['consumable'] = \App\Models\Consumable::count();
        $counts['activity'] = isset($monthlyStats["activity"]) ? $monthlyStats["activity"]["total-count"] : "0";
        $counts['activity_active'] = isset($monthlyStats["activity"]) ? $monthlyStats["activity"]["active-count"] : "0";
        $counts['activity_overdue'] = isset($monthlyStats["activity"]) ? $monthlyStats["activity"]["overdue-count"] : "0";
        $counts['activity_co_prev_month'] = isset($monthlyStats["prevMonth"]["activity"]) ? $monthlyStats["prevMonth"]["activity"]["checkout-count"] : "0";
        $counts['activity_ci_prev_month'] = isset($monthlyStats["prevMonth"]["activity"]) ? $monthlyStats["prevMonth"]["activity"]["checkin-count"] : "0";
        $counts['activity_co_curr_month'] = isset($monthlyStats["activity"]) ? $monthlyStats["activity"]["checkout-count"] : "0";
        $counts['activity_ci_curr_month'] = isset($monthlyStats["activity"]) ? $monthlyStats["activity"]["checkin-count"] : "0";
        $counts['grand_total'] = $counts['asset'] + $counts['accessory'] + $counts['consumable'];
        $counts['membership'] = array();
        $counts['membership']['regular'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["Regular"]) ? $monthlyStats["membership"]["Regular"]["active-count"] : "0";
        $counts['membership']['renewal'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["Renewal"])? $monthlyStats["membership"]["Renewal"]["active-count"] : "0";
        $counts['membership']['reduced_regular'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["RegularReduced"])? $monthlyStats["membership"]["RegularReduced"]["active-count"] : "0";
        $counts['membership']['reduced_renewal'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["RenewalReduced"])? $monthlyStats["membership"]["RenewalReduced"]["active-count"] : "0";
        $counts['membership']['org_regular'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["RegularOrg"])? $monthlyStats["membership"]["RegularOrg"]["active-count"] : "0";
        $counts['membership']['org_renewal'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["RenewalOrg"])? $monthlyStats["membership"]["RenewalOrg"]["active-count"] : "0";
        $counts['membership']['temporary'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["Temporary"]) ? $monthlyStats["membership"]["Temporary"]["active-count"] : "0";
        $counts['membership']['active'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["all"]) ? $monthlyStats["membership"]["all"]["active-count"] : "0";
        $counts['membership']['expired'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["all"]) ? $monthlyStats["membership"]["all"]["expired-count"] : "0";
        $counts['membership']['pending'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["all"]) ? $monthlyStats["membership"]["all"]["pending-count"] : "0";
        $counts['membership']['cancelled'] = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["all"]) ? $monthlyStats["membership"]["all"]["cancelled-count"] : "0";
        $prevNewActiveMembers = isset($monthlyStats["prevMonth"]) && isset($monthlyStats["prevMonth"]["membership"]) && isset($monthlyStats["prevMonth"]["membership"]["all"])
                ? $monthlyStats["prevMonth"]["membership"]["all"]["new-active-count"] : 0;
        $prevNewPendingMembers = isset($monthlyStats["prevMonth"]) && isset($monthlyStats["prevMonth"]["membership"]) && isset($monthlyStats["prevMonth"]["membership"]["all"])
                ? $monthlyStats["prevMonth"]["membership"]["all"]["new-pending-count"] : 0;
        $counts['membership']['new_members_prev_month'] = $prevNewActiveMembers + $prevNewPendingMembers;
        $newActiveMembers = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["all"]) ? $monthlyStats["membership"]["all"]["new-active-count"] : 0;
        $newPendingMembers = isset($monthlyStats["membership"]) && isset($monthlyStats["membership"]["all"]) ? $monthlyStats["membership"]["all"]["new-pending-count"] : 0;
        $counts['membership']['new_members_curr_month'] = $newActiveMembers + $newPendingMembers;

//        return view('dashboard')->with('asset_stats', $asset_stats)->with('counts', $counts);
        return view('klusbib::dashboard')->with('counts', $counts);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('klusbib::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('klusbib::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('klusbib::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
