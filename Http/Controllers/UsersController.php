<?php
namespace Modules\Klusbib\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Group;
use App\Models\User;
use Artisan;
use Auth;
use Config;
use Crypt;
use DB;
use Gate;
use HTML;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Input;
use Lang;
use Mail;
use Modules\Klusbib\Http\KlusbibApi;
use Str;
use URL;
use View;

/**
 * This controller handles all actions related to Users for
 * the Snipe-IT Asset Management application. (Klusbib extension module)
 *
 */


class UsersController extends Controller
{

    use AuthorizesRequests;

    /**
    * Returns a view that invokes the ajax tables which actually contains
    * the content for the users listing, which is generated in getDatatable().
    *
    * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
//        $this->authorize('index', User::class);
        return view('klusbib::users/index');
    }

    /**
     * Returns a view that displays the edit user form
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @since [v1.0]
     * @param $permissions
     * @return View
     * @internal param int $id
     */
    public function edit($id)
    {
        // TODO: enrich with API data
        if ($user =  User::find($id)) {

            $this->authorize('update', $user);
            $permissions = config('permissions');

            $groups = Group::pluck('name', 'id');

            $userGroups = $user->groups()->pluck('name', 'id');
            $user->permissions = $user->decodePermissions();
            $userPermissions = Helper::selectedPermissionsArray($permissions, $user->permissions);
            $permissions = $this->filterDisplayable($permissions);

            return view('klusbib::users/edit', compact('user', 'groups', 'userGroups', 'permissions', 'userPermissions'))->with('item', $user);
        }

        $error = trans('admin/users/message.user_not_found', compact('id'));
        return redirect()->route('klusbib::users/index')->with('error', $error);


    }
    private function filterDisplayable($permissions)
    {
        $output = null;
        foreach ($permissions as $key => $permission) {
            $output[$key] = array_filter($permission, function ($p) {
                return $p['display'] === true;
            });
        }
        return $output;
    }

}
