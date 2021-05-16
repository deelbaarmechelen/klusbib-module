<?php

namespace Modules\Klusbib\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Klusbib\Http\KlusbibApi;
use Modules\Klusbib\Models\Api\Lending;

class LendingsController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the reservations listing, which is generated in getDatatable().
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->authorize('index', Lending::class);
        return view('klusbib::lendings/index');
    }

}