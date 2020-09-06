<?php

namespace Modules\Klusbib\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Klusbib\Http\KlusbibApi;

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
//        $users = \Modules\Klusbib\Models\Api\User::all();
        return view('klusbib::memberships/index');
    }

}