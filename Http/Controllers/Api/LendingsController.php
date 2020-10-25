<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Transformers\LendingsTransformer;
use Modules\Klusbib\Http\Transformers\ReservationsTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Klusbib\Models\Api\Lending;

class LendingsController extends Controller
{
    const CAT_MAX_VALUES = 10;

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
//        $this->authorize('view', \Modules\Klusbib\Models\Api\Lending::class);

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $requestedSort = $request->get('sort');
        Log::debug("Requested sort order = " . $requestedSort);
        $allowed_columns =
            [
                'lending_id', 'tool_id', 'user_id', 'username', 'startsAt', 'dueAt', 'returnedAt', 'type', 'comment'
            ];

        $sort = in_array($requestedSort, $allowed_columns) ? $requestedSort : 'lending_id'; // default sort on lending id (chronological order)
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
        $lendingsPaginator = \Modules\Klusbib\Models\Api\Lending::all($params);

        foreach ($lendingsPaginator->items() as $lending) {
            $user = User::where('employee_num', '=', $lending->user_id)->get();
//            Log::debug("User for id " . $lending->user_id . ": " . \json_encode($user));
            $lending->user = count($user) > 0 ? $user[0] : null;
            $tool = Asset::find(intval($lending->tool_id));
//            Log::debug("Tool for id " . $lending->tool_id . ": " . \json_encode($tool));
            $lending->tool = $tool;
        }
//        $users = $users->skip($offset)->take($limit)->get();
        return (new LendingsTransformer)->transformLendings($lendingsPaginator->items(), $lendingsPaginator->total());
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
        Log::debug("Api/LendingsController::show for id " . $id);
        $this->authorize('view', Lending::class);
        $lending = Lending::findOrFail($id);
        if ($lending == null || $lending->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Lending unknown'), 404);
        }
        $klusbibLending = \Modules\Klusbib\Models\Api\Lending::find($lending->lending_id);

        return (new LendingsTransformer)->transformLending($klusbibLending);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        Log::debug('Api/LendingsController edit for id ' . $id);
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
        Log::debug('Api/LendingsController update for id ' . $id);
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);

//        $this->authorize('update', Lending::class);
//        $snipeLending= Lending::find($id);
//        if ($snipeLending == null) {
//            return response()->json(Helper::formatStandardApiResponse('error', null, 'Lending unknown'), 404);
//        }
//        if (isset($snipeLending->employee_num)) {
//            $lending = \Modules\Klusbib\Models\Api\Lending::find($snipeLending->employee_num);
//        }
//        if ($lending == null || $snipeLending->employee_num == null) {
//            return response()->json(Helper::formatStandardApiResponse('error', null, 'API Lending unknown or no Klusbib id (employee_num)'), 404);
//        }
//        Log::debug('Api/LendingsController update lending found: ' . $lending);
//        Log::debug('Api/LendingsController lending saved');
//        Log::debug('Api/LendingsController transform lending');
//        return (new LendingsTransformer)->transformLending($lending);
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

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getLendingsByDueDate()
    {
        $limit = config('app.max_results');
        $params = array();
        $params["_perPage"] = $limit;
        $params["_page"] = 1;
        $params["_sortDir"] = 'desc';
        $params["_sortField"] = 'due_date';
        $params["active"] = 'true';
        $lendingsPaginator = \Modules\Klusbib\Models\Api\Lending::all($params);

        $labels=[];
        $points=[];
        foreach ($lendingsPaginator->items() as $lending) {
            $dueDateLabelIndex = array_search($lending->due_date, $labels);
            if ($dueDateLabelIndex !== FALSE) {
                $points[$dueDateLabelIndex]=$points[$dueDateLabelIndex]+1;
            } else {
                $labels[]=$lending->due_date;
                $points[]=1;
            }
        }

        $rows = array();
        foreach ($lendingsPaginator->items() as $lending) {
            $user = \json_decode($lending->user);
            $row = array("id" => $lending->lending_id,
                "start_date" => $lending->start_date,
                "due_date" => $lending->due_date,
                "returned_date" => $lending->returned_date,
                "user_id" => $lending->user_id,
                "user" => (isset($lending->user) ? \json_decode($lending->user) : null),
                "username" => (isset($lending->user) ? $user->firstname . ' ' . $user->lastname : ""),
                "tool_id" => $lending->tool_id,
                "tool_type" => $lending->tool_type,
                "tool" => (isset($lending->tool) ? \json_decode($lending->tool) : null) );
            array_push($rows, $row);
        }
        $colors_array = Helper::chartColors();

        $chart= [
            "labels" => $labels,
            "datasets" => array ([
                "label" => "verwacht incheckdatum",
                "data" => $points,
                "backgroundColor" => $colors_array[0],
                "hoverBackgroundColor" =>  $colors_array[0],
                "barPercentage" => 0.5,
                "barThickness" => 6,
                "maxBarThickness" => 8,
                "minBarLength" => 2,
                "borderWidth" => 1
            ])
        ];
        return array (
            "total" => count($rows),
            "rows" => $rows,
            "chart" => $chart
        );
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function getLendingsByCategory()
    {
        $limit = config('app.max_results');
        $params = array();
        $params["_perPage"] = $limit;
        $params["_page"] = 1;
        $params["_sortDir"] = 'asc';
        $params["_sortField"] = 'due_date';
        $lendingsPaginator = \Modules\Klusbib\Models\Api\Lending::all($params);

        $labels=[];
        $catCount=[];
        foreach ($lendingsPaginator->items() as $lending) {
//            $tool = \json_decode($lending->tool);
            if (isset($lending->tool_id) && $lending->tool_type == "TOOL" ) {
                $asset = Asset::find($lending->tool_id);
//                echo \json_encode($asset->model->category);
                $category = (isset($asset->model) ? $asset->model->category->name : "unknown");
                if (isset($catCount[$category])) {
                    $catCount[$category] += 1;
                } else {
                    $catCount[$category] = 1;
                }
            }
        }
        // Limit results to top 10 categories, others should be grouped in 'Others' category
        arsort($catCount); // sort by value
        $catTop = array_slice($catCount, 0, self::CAT_MAX_VALUES);
        $catOthers = array_slice($catCount, self::CAT_MAX_VALUES);
        foreach($catTop as $cat => $count) {
            $labels[] = $cat;
            $points[] = $count;
        }
        $otherCount = 0;
        foreach ($catOthers as $count) {
            $otherCount += $count;
        }
        // FIXME: Others is much to big to be shown next to other results and makes the chart unreadable
        if ($otherCount > 0) {
            $labels[] = "Others";
            $points[] = $otherCount;
        }

        $colors_array = Helper::chartColors();
        $chart= [
            "labels" => $labels,
            "datasets" => array ([
                "label" => "Aantal ontleningen",
                "data" => $points,
                "backgroundColor" => $colors_array[0],
                "hoverBackgroundColor" =>  $colors_array[0],
                "barPercentage" => 0.5,
                "barThickness" => 6,
                "maxBarThickness" => 8,
                "minBarLength" => 2,
                "borderWidth" => 1
            ])
        ];
        return array (
//            "total" => count($rows),
//            "rows" => $rows,
            "chart" => $chart
        );
    }

}
