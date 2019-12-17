<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use Modules\Klusbib\Http\Transformers\UsersTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Klusbib\Http\KlusbibApi;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $users = KlusbibApi::instance()->getUsers();
        $total = count($users);
//        $users = $users->skip($offset)->take($limit)->get();
        return (new UsersTransformer)->transformUsers($users, $total);
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
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
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
