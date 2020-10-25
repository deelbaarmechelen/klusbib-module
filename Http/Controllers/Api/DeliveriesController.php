<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Transformers\DeliveriesTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class DeliveriesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
//        $this->authorize('view', \Modules\Klusbib\Models\Api\Delivery::class);

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $requestedSort = $request->get('sort');
        Log::debug("Requested sort order = " . $requestedSort);
        if ($requestedSort == 'user') { // no sort on user available in API, sort on username instead
            $requestedSort = 'username';
        }
        if ($requestedSort == 'tool') { // no sort on tool available in API, sort on tool_id instead
            $requestedSort = 'tool_id';
        }
        $allowed_columns =
            [
                'delivery_id', 'tool_id', 'user_id', 'username', 'state', 'startsAt', 'endsAt', 'type', 'comment',
                'title'
            ];

        $sort = in_array($requestedSort, $allowed_columns) ? $requestedSort : 'delivery_id'; // default sort on delivery id (chronological order)
        Log::debug("Real sort order = " . $sort);
        $offset = request('offset', 0);

        // Check to make sure the limit is not higher than the max allowed
        ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit')))
            ? $limit = $request->input('limit') : $limit = config('app.max_results');
//        if (($request->filled('deleted')) && ($request->input('deleted')=='true')) {
//            $deliveriesPaginator = $deliveriesPaginator->GetDeleted();
//        }

        $params = array();
        $params["_perPage"] = $limit;
        $params["_page"] = intdiv($offset, $limit) +1;
        $params["_sortDir"] = $order;
        $params["_sortField"] = $sort;
        if ($request->filled('search')) {
            $params["_query"] = $request->input('search');
        }
        if (($request->filled('deleted')) && ($request->input('deleted')=='true')) {
            $params["state"] = 'DELETED';
        }
        $deliveriesPaginator = \Modules\Klusbib\Models\Api\Delivery::all($params);

        foreach ($deliveriesPaginator->items() as $delivery) {
            $user = User::where('employee_num', '=', $delivery->user_id)->get();
//            Log::debug("User for id " . $delivery->user_id . ": " . \json_encode($user));
            $delivery->user = count($user) > 0 ? $user[0] : null;
            $tool = Asset::find(intval($delivery->tool_id));
//            Log::debug("Tool for id " . $delivery->tool_id . ": " . \json_encode($tool));
            $delivery->tool = $tool;
        }
//        $users = $users->skip($offset)->take($limit)->get();
        return (new DeliveriesTransformer)->transformDeliveries($deliveriesPaginator->items(), $deliveriesPaginator->total());
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
        Log::debug("Api/DeliveriesController::show for id " . $id);
        $this->authorize('view', Delivery::class);
        $delivery = Delivery::findOrFail($id);
        if ($delivery == null || $delivery->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Delivery unknown or no Klusbib id (employee_num)'), 404);
        }
//        $klusbibDelivery = KlusbibApi::instance()->getDelivery($delivery->employee_num);
        $klusbibDelivery = \Modules\Klusbib\Models\Api\Delivery::find($delivery->employee_num);

        return (new DeliveriesTransformer)->transformDelivery($klusbibDelivery);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        Log::debug('Api/DeliveriesController edit for id ' . $id);
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
        // TODO: get body to update received data
        Log::debug('Api/DeliveriesController update for id ' . $id);
        $this->authorize('update', Delivery::class);
        $snipeDelivery = Delivery::find($id);
        if ($snipeDelivery == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Delivery unknown'), 404);
        }
        if (isset($snipeDelivery->employee_num)) {
            $delivery = \Modules\Klusbib\Models\Api\Delivery::find($snipeDelivery->employee_num);
        }
        if ($delivery == null || $snipeDelivery->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'API Delivery unknown or no Klusbib id (employee_num)'), 404);
        }
        Log::debug('Api/DeliveriesController update delivery found: ' . $delivery);
//        $delivery->address = 'test2';
//        $delivery->save();
        Log::debug('Api/DeliveriesController delivery saved');
        Log::debug('Api/DeliveriesController transform delivery');
        return (new DeliveriesTransformer)->transformDelivery($delivery);
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
