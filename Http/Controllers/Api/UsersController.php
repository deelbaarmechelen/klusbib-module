<?php

namespace Modules\Klusbib\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Transformers\SelectlistTransformer;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Transformers\UsersTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Torann\RemoteModel\Model;

class UsersController extends Controller
{
    /**
     * Sync user data from Klusbib API to this inventory
     */
    public function syncNew(Request $request) {
        //Model::getClient()->updateToken($request->session());
        $state = $request->input('state');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $username = $request->input('username');
        $employee_num = $request->input('employee_num');
        if (!$request->has('employee_num')) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User employee_num is missing (user_id)'), 400);
        }
        if (!$request->has('username')) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User username is missing (email)'), 400);
        }
        if (!$request->has('state')) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User state is missing'), 400);
        }
        $existingUser = User::where('employee_num', $employee_num)->first();
        if ($existingUser === null) {
            $user = new User();
            // generate random password
            $tmp_pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 20);
            $user->password = bcrypt($request->get('password', $tmp_pass));
            // force company to Klusbib
            $user->company_id = 1;
            $user->employee_num = $employee_num;
        } else {
            $user = $existingUser;
        }

        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->username = $username;
        if ($state == 'ACTIVE') {
            // avatar should be placed in public/uploads/avatar directory
            $user->avatar = \Modules\Klusbib\Models\Api\User::STATE_ACTIVE_AVATAR;
        } else {
            $user->avatar = \Modules\Klusbib\Models\Api\User::STATE_INACTIVE_AVATAR;
        }

        if ($user->save()) {
            return response()->json(Helper::formatStandardApiResponse('success', (new UsersTransformer)->transformSyncedUser($user), trans('admin/users/message.success.update')));
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Unable to save user'), 500);

    }
    /**
     * Sync user data from Klusbib API to this inventory
     */
    public function syncDelete(Request $request, $id) {
        //Model::getClient()->updateToken($request->session());
        if (is_null($user = User::find($id))) {
            // Redirect to the models management page
            return response()->json(Helper::formatStandardApiResponse('success', null, 'User not longer exists'), 200);
        }
        if ($user->delete()) {
            return response()->json(Helper::formatStandardApiResponse('success', (new UsersTransformer)->transformSyncedUser($user), trans('admin/users/message.success.delete')));
        }
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Unable to save user'), 500);
    }
    /**
     * Sync user data from Klusbib API to this inventory
     */
    public function syncUpdate(Request $request, $id) {
        Log::debug("UsersController Sync Update for id $id");
        //Model::getClient()->updateToken($request->session());
        if (is_null($user = User::find($id))) {
            // Redirect to the models management page
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User not found'), 200);
        }
        $state = $request->input('state');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $username = $request->input('username');
        $employee_num = $request->input('employee_num');
        if (!$request->has('employee_num')) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User employee_num is missing (user_id)'), 400);
        }
        if (!$request->has('username')) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User username is missing (email)'), 400);
        }
        if (!$request->has('state')) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'User state is missing'), 400);
        }
        if ( (strcasecmp($username, $user->username) == 0)
            && User::getNotDeleted()->where('username', '=', $username)->count() > 1 ) {
            return response()->json(Helper::formatStandardApiResponse('error', null, 'Username not unique (' . $username . ')'), 400);
        }
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->username = $username;
        $user->employee_num = $employee_num;
        if ($state == 'ACTIVE') {
            // avatar should be placed in public/uploads/avatar directory
            $user->avatar = \Modules\Klusbib\Models\Api\User::STATE_ACTIVE_AVATAR;
        } else {
            $user->avatar = \Modules\Klusbib\Models\Api\User::STATE_INACTIVE_AVATAR;
        }
        Log::debug("UsersController Sync Update before save");
        if ($user->save()) {
            Log::debug("UsersController Sync Update after save");
            return response()->json(Helper::formatStandardApiResponse('success', (new UsersTransformer)->transformSyncedUser($user), trans('admin/users/message.success.update')));
        }
        $query = $user->newModelQuery();

        Log::error('Unable to save user ' . \json_encode($query) . ' - user: ' . \json_encode($user));
        return response()->json(Helper::formatStandardApiResponse('error', null, 'Unable to save user'), 500);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        // FIXME: remove explicit call to updateToken?? Or needed to query all users (line 174)?
        Model::getClient()->updateToken($request->session());
//        $this->authorize('view', \Modules\Klusbib\Models\Api\User::class);

        //        $users = KlusbibApi::instance()->getUsers();

        $order = $request->input('order') === 'asc' ? 'asc' : 'desc';
        $requestedSort = $request->get('sort');
        Log::debug("Requested sort order = " . $requestedSort);
        if ($requestedSort == 'name') { // name is concatenation of firstname and lastname
            $requestedSort = 'firstname';
        }
        $allowed_columns =
            [
                'user_id', 'state', 'firstname', 'lastname', 'email', 'email_state',
                'role','membership_start_date','membership_end_date','address' ,'postal_code' ,'city' ,'phone' ,'mobile' ,'registration_number',
                'payment_mode' ,'accept_terms_date' ,'created_at' ,'updated_at', 'id'
            ];

        $sort = in_array($requestedSort, $allowed_columns) ? $requestedSort : 'firstname';
        Log::debug("Real sort order = " . $sort);
        $offset = request('offset', 0);

        // Check to make sure the limit is not higher than the max allowed
        ((config('app.max_results') >= $request->input('limit')) && ($request->filled('limit')))
            ? $limit = $request->input('limit') : $limit = config('app.max_results');
//        if (($request->filled('deleted')) && ($request->input('deleted')=='true')) {
//            $usersPaginator = $usersPaginator->GetDeleted();
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
        $usersPaginator = \Modules\Klusbib\Models\Api\User::all($params);


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
        // FIXME: remove explicit call to updateToken?? Or needed for check on employee_num (line 247)?
        Model::getClient()->updateToken($request->session());
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


    /**
     * Gets a paginated collection for the select2 menus
     * @see https://select2.org/data-sources/formats
     * @see \App\Http\Transformers\SelectlistTransformer
     *
     */
    public function selectlist(Request $request)
    {
        Log::debug('Klusbib: Api/UsersController selectlist');
        //lookup all data locally, no need to call api
        $users = User::select(
            [
                'users.id',
                'users.username',
                'users.employee_num',
                'users.first_name',
                'users.last_name',
                'users.gravatar',
                'users.avatar',
                'users.email',
            ]
        )->where('show_in_list', '=', '1')
         ->whereNotNull('employee_num');

        $users = Company::scopeCompanyables($users);

        if ($request->filled('search')) {
            $users = $users->SimpleNameSearch($request->get('search'))
                ->orWhere('username', 'LIKE', '%'.$request->get('search').'%')
                ->orWhere('employee_num', 'LIKE', '%'.$request->get('search').'%');
        }

        $users = $users->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
        $users = $users->paginate(50);

        foreach ($users as $user) {
            $name_str = '';
            if ($user->last_name!='') {
                $name_str .= e($user->last_name).', ';
            }
            $name_str .= e($user->first_name);

            if ($user->username!='') {
                $name_str .= ' ('.e($user->username).')';
            }

            if ($user->employee_num!='') {
                $name_str .= ' - #'.e($user->employee_num);
            }
            $user->id = $user->employee_num; // force id to employee_num as this is the primary key used in klusbib API
            $user->use_text = $name_str;
            $user->use_image = ($user->present()->gravatar) ? $user->present()->gravatar : null;
        }

        return (new SelectlistTransformer)->transformSelectlist($users);

    }

}
