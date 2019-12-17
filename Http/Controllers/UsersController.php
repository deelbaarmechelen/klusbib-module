<?php
namespace Modules\Klusbib\Http\Controllers;

use App\Models\User;
use Artisan;
use Auth;
use Config;
use Crypt;
use DB;
use Gate;
use HTML;
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


    /**
    * Returns a view that invokes the ajax tables which actually contains
    * the content for the users listing, which is generated in getDatatable().
    *
    * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $users = KlusbibApi::instance()->getUsers();
//        $this->authorize('index', User::class);
        return view('klusbib::users/index');
    }

}
