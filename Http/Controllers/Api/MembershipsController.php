<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Transformers\LendingsTransformer;
use Modules\Klusbib\Http\Transformers\MembershipsTransformer;
use Modules\Klusbib\Http\Transformers\ReservationsTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Klusbib\Models\Api\Lending;

class MembershipsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
//        $this->authorize('view', \Modules\Klusbib\Models\Api\Membership::class);

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $requestedSort = $request->get('sort');
        Log::debug("Requested sort order = " . $requestedSort);
        $allowed_columns =
            [
                'user_id', 'username', 'type', 'comment'
            ];

        $sort = in_array($requestedSort, $allowed_columns) ? $requestedSort : 'id'; // default sort on id (chronological order)
        Log::debug("Real sort order = " . $sort);
        $offset = request('offset', 0);

        // Check to make sure the limit is not higher than the max allowed
        ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit')))
            ? $limit = $request->input('limit') : $limit = config('app.max_results');

        $params = array();
        $params["_perPage"] = $limit;
        $params["_page"] = intdiv($offset, $limit) +1;
        $params["_sortDir"] = $order;
        $params["_sortField"] = $sort;
        if ($request->filled('user_id') ) {
            $params["user_id"] = $request->input('user_id');
        }
        if ($request->filled('status') ) {
            $params["status"] = $request->input('status');
        } else {
            $params["status"] = 'ALL';
        }
        if ($request->filled('search') ) {
            $params["_query"] = $request->input('search');
        }
        $membershipsPaginator = \Modules\Klusbib\Models\Api\Membership::all($params);

        foreach ($membershipsPaginator->items() as $membership) {
            $user = User::where('employee_num', '=', $membership->contact_id)->get();
            Log::debug("User for id " . $membership->contact_id . ": " . \json_encode($user));
            $membership->user = count($user) > 0 ? $user[0] : null;
        }
//        $users = $users->skip($offset)->take($limit)->get();
        return (new MembershipsTransformer())->transformMemberships($membershipsPaginator->items(), $membershipsPaginator->total());
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        Log::debug("Api/MembershipsController::show for id " . $id);
        $this->authorize('view', Membership::class);
        $membership = Membership::findOrFail($id);
        if ($membership == null || $membership->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Membership unknown'), 404);
        }
        $klusbibMembership = \Modules\Klusbib\Models\Api\Membership::find($membership->membership_id);

        return (new MembershipsTransformer)->transformMembership($klusbibMembership);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        Log::debug('Api/MembershipsController edit for id ' . $id);
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        Log::debug('Api/MembershipsController update for id ' . $id);
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
    }

}
