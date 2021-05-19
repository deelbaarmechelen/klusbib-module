<?php

namespace Modules\Klusbib\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Klusbib\Http\KlusbibApi;
use Modules\Klusbib\Models\Api\Membership;

class MembershipsController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the reservations listing, which is generated in getDatatable().
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->authorize('index', Membership::class);
//        $users = \Modules\Klusbib\Models\Api\User::all();
        return view('klusbib::memberships/index');
    }

}