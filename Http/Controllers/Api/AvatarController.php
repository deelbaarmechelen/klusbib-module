<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Transformers\UsersTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class AvatarController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
//        return view('klusbib::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
//        return view('klusbib::create');
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
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
//        return view('klusbib::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
//        return view('klusbib::edit');
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
//        $this->authorize('update', AssetModel::class);
        // Check if the model exists
        if (is_null($user = User::find($id))) {
            // Redirect to the models management page
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User not found'), 200);
        }
        $status = $request->input('status');
        if (!$request->has('status')) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User status is missing'), 400);
        }
        if ($status == 'ACTIVE') {
        // avatar should be placed in public/uploads/avatar directory
            $user->avatar = "DBM_avatar_ok.png";
        } else {
            $user->avatar = "DBM_avatar_nok.png";
        }
        if ($user->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', (new UsersTransformer)->transformUser($user), trans('admin/users/message.success.update')));
        }
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
