<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Transformers\ReservationsTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ReservationsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
//        $this->authorize('view', \Modules\Klusbib\Models\Api\Reservation::class);

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
                'reservation_id', 'tool_id', 'user_id', 'username', 'state', 'startsAt', 'endsAt', 'type', 'comment',
                'title'
            ];

        $sort = in_array($requestedSort, $allowed_columns) ? $requestedSort : 'reservation_id'; // default sort on reservation id (chronological order)
        Log::debug("Real sort order = " . $sort);
        $offset = request('offset', 0);

        // Check to make sure the limit is not higher than the max allowed
        ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit')))
            ? $limit = $request->input('limit') : $limit = config('app.max_results');
//        if (($request->filled('deleted')) && ($request->input('deleted')=='true')) {
//            $reservationsPaginator = $reservationsPaginator->GetDeleted();
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
        $reservationsPaginator = \Modules\Klusbib\Models\Api\Reservation::all($params);

        foreach ($reservationsPaginator->items() as $reservation) {
            $user = User::where('employee_num', '=', $reservation->user_id)->get();
//            Log::debug("User for id " . $reservation->user_id . ": " . \json_encode($user));
            $reservation->user = count($user) > 0 ? $user[0] : null;
            $tool = Asset::find(intval($reservation->tool_id));
//            Log::debug("Tool for id " . $reservation->tool_id . ": " . \json_encode($tool));
            $reservation->tool = $tool;
        }
//        $users = $users->skip($offset)->take($limit)->get();
        return (new ReservationsTransformer)->transformReservations($reservationsPaginator->items(), $reservationsPaginator->total());
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
        Log::debug("Api/ReservationsController::show for id " . $id);
        $this->authorize('view', Reservation::class);
        $reservation = Reservation::findOrFail($id);
        if ($reservation == null || $reservation->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Reservation unknown or no Klusbib id (employee_num)'), 404);
        }
//        $klusbibReservation = KlusbibApi::instance()->getReservation($reservation->employee_num);
        $klusbibReservation = \Modules\Klusbib\Models\Api\Reservation::find($reservation->employee_num);

        return (new ReservationsTransformer)->transformReservation($klusbibReservation);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        Log::debug('Api/ReservationsController edit for id ' . $id);
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
        Log::debug('Api/ReservationsController update for id ' . $id);
        $this->authorize('update', Reservation::class);
        $snipeReservation = Reservation::find($id);
        if ($snipeReservation == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Reservation unknown'), 404);
        }
        if (isset($snipeReservation->employee_num)) {
            $reservation = \Modules\Klusbib\Models\Api\Reservation::find($snipeReservation->employee_num);
        }
        if ($reservation == null || $snipeReservation->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'API Reservation unknown or no Klusbib id (employee_num)'), 404);
        }
        Log::debug('Api/ReservationsController update reservation found: ' . $reservation);
//        $reservation->address = 'test2';
//        $reservation->save();
        Log::debug('Api/ReservationsController reservation saved');
        Log::debug('Api/ReservationsController transform reservation');
        return (new ReservationsTransformer)->transformReservation($reservation);
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
