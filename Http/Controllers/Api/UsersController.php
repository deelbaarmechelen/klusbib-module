<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Transformers\UsersTransformer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
//        $users = KlusbibApi::instance()->getUsers();
        $usersPaginator = \Modules\Klusbib\Models\Api\User::all();
//        $users = $users->skip($offset)->take($limit)->get();
        return (new UsersTransformer)->transformUsers($usersPaginator->items(), $usersPaginator->total());
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
        Log::debug("Api/UsersController::show for id " . $id);
        $this->authorize('view', User::class);
        $user = User::findOrFail($id);
        if ($user == null || $user->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User unknown or no Klusbib id (employee_num)'), 404);
        }
//        $klusbibUser = KlusbibApi::instance()->getUser($user->employee_num);
        $klusbibUser = \Modules\Klusbib\Models\Api\User::find($user->employee_num);

        return (new UsersTransformer)->transformUser($klusbibUser);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        Log::debug('Api/UsersController edit for id ' . $id);
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
        Log::debug('Api/UsersController update for id ' . $id);
        $this->authorize('update', User::class);
        $snipeUser = User::find($id);
        if ($snipeUser == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User unknown'), 404);
        }
        if (isset($snipeUser->employee_num)) {
            $user = \Modules\Klusbib\Models\Api\User::find($snipeUser->employee_num);
        }
        if ($user == null || $snipeUser->employee_num == null) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'API User unknown or no Klusbib id (employee_num)'), 404);
        }
        Log::debug('Api/UsersController update user found: ' . $user);
        $user->address = 'test2';
        $user->save();
        Log::debug('Api/UsersController user saved');
        Log::debug('Api/UsersController transform user');
        return (new UsersTransformer)->transformUser($user);
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
