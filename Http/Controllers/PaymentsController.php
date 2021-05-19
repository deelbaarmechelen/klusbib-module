<?php

namespace Modules\Klusbib\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Klusbib\Http\KlusbibApi;
use Modules\Klusbib\Models\Api\Payment;

class PaymentsController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the reservations listing, which is generated in getDatatable().
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->authorize('index', Payment::class);
        return view('klusbib::payments/index');
    }

}