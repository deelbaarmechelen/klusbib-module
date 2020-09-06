<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Transformers\LendingsTransformer;
use Modules\Klusbib\Http\Transformers\PaymentsTransformer;
use Modules\Klusbib\Http\Transformers\ReservationsTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Klusbib\Models\Api\Lending;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
//        $this->authorize('view', \Modules\Klusbib\Models\Api\Payment::class);

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $requestedSort = $request->get('sort');
        Log::debug("Requested sort order = " . $requestedSort);
        $allowed_columns =
            [
                'payment_id', 'user_id', 'username', 'state', 'mode', 'amount', 'currency', 'comment'
            ];

        $sort = in_array($requestedSort, $allowed_columns) ? $requestedSort : 'payment_id'; // default sort on payment id (chronological order)
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
        if ($request->filled('search')) {
            $params["_query"] = $request->input('search');
        }
        $paymentsPaginator = \Modules\Klusbib\Models\Api\Payment::all($params);

        foreach ($paymentsPaginator->items() as $payment) {
            $user = User::where('employee_num', '=', $payment->user_id)->get();
//            Log::debug("User for id " . $payment->user_id . ": " . \json_encode($user));
            $payment->user = count($user) > 0 ? $user[0] : null;
            $tool = Asset::find(intval($payment->tool_id));
//            Log::debug("Tool for id " . $payment->tool_id . ": " . \json_encode($tool));
            $payment->tool = $tool;
        }
//        $users = $users->skip($offset)->take($limit)->get();
        return (new PaymentsTransformer())->transformPayments($paymentsPaginator->items(), $paymentsPaginator->total());
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
        Log::debug("Api/PaymentsController::show for id " . $id);
        $this->authorize('view', Payment::class);
        $payment = Payment::findOrFail($id);
        if ($payment == null || $payment->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Payment unknown'), 404);
        }
        $klusbibPayment = \Modules\Klusbib\Models\Api\Payment::find($payment->payment_id);

        return (new PaymentsTransformer)->transformPayment($klusbibPayment);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        Log::debug('Api/PaymentsController edit for id ' . $id);
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
        Log::debug('Api/PaymentsController update for id ' . $id);
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
