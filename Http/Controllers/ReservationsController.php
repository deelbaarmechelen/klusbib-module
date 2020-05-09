<?php

namespace Modules\Klusbib\Http\Controllers;

use App\Helpers\Helper;
//use Illuminate\Routing\Controller;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Modules\Klusbib\Http\KlusbibApi;
use Modules\Klusbib\Models\Api\Reservation;

class ReservationsController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the reservations listing, which is generated in getDatatable().
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('klusbib::reservations/index');
    }
    public function export()
    {
//        return response()->json(Helper::formatStandardApiResponse('error', null, 'Not yet implemented'), 200);
    }
    /**
     * Returns a form view that allows an admin to create a new licence.
     *
     * @see AccessoriesController::getDatatable() method that generates the JSON response
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->authorize('create', Reservation::class);

        return view('klusbib::reservations/edit')
            //->with('reservation_options',$reservation_options)
            ->with('item', new Reservation());

    }

    /**
     * Validates and stores the reservation form data submitted from the new
     * reservation form.
     *
     * @see ReservationsController::create() method that provides the form view
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Reservation::class);

        // create a new model instance
        $reservation = new Reservation();
        // Save the reservation data
        $reservation->start_date   = $request->input('start_date');
        $reservation->end_date          = $request->input('end_date');
//        $reservation->name              = $request->input('name');
        $reservation->comment           = $request->input('notes');
        $reservation->tool_id           = $request->input('tool_id');
        $reservation->state             = $request->input('state');
        $reservation->user_id           = $request->input('user_id');
        $reservation->cancel_reason     = $request->input('cancel_reason');
        Log::info('Reservation: ' . \json_encode($reservation));
//        $reservation->user_id           = Auth::id();

        if ($reservation->save()) {
            return redirect()->route("klusbib.reservations.index")->with('success', trans('klusbib::admin/reservations/message.create.success'));
        }
//        return redirect()->back()->withInput()->withErrors($reservation->getErrors());
        $errorMessage = trans('klusbib::admin/reservations/message.create.error');
        $errors = Arr::get($reservation->getClientError(), 'errors');
        if (is_array($errors)) {
            $errorMessage .= " (API fout: ";
            foreach($errors as $key => $value) {
                if (\is_string($key)) {
                    $errorMessage .= $key . ": ";
                }
                $errorMessage .= $value;
            }
            $errorMessage .= ")";
        }
        // Show generic failure message
        return redirect()->back()->withInput()
            ->with('error', $errorMessage);
    }

    public function cancel($reservationId = null)
    {
        $this->authorize('update', Reservation::class);
        if (is_null($item = Reservation::find($reservationId))) {
            return redirect()->route('klusbib.reservations.index')->with('error', trans('klusbib::admin/reservations/message.does_not_exist'));
        }
//        $reservation = Reservation::find($reservationId);
//        if (is_null($reservation)) {
//            return redirect()->route('klusbib.reservations.index')->with('error', trans('klusbib::admin/reservations/message.does_not_exist'));
//        }
        $item->id = $item->reservation_id; // view expects id to be set to distinguish between create (POST) and update (PUT)
        $item->state = 'CANCELLED';
        return view('klusbib::reservations/edit', compact('item'));

//        if ($reservation->save()) {
//            return redirect()->route("klusbib.reservations.index")->with('success', trans('klusbib::admin/reservations/message.update.success'));
//        }
//        return redirect()->back()->withInput()->withErrors($reservation->getErrors());
    }
    public function confirm($reservationId = null)
    {
        $this->authorize('update', Reservation::class);
        $reservation = Reservation::find($reservationId);
        if (is_null($reservation)) {
            return redirect()->route('klusbib.reservations.index')->with('error', trans('klusbib::admin/reservations/message.does_not_exist'));
        }
        $reservation->state = 'CONFIRMED';
        if ($reservation->save()) {
            return redirect()->route("klusbib.reservations.index")->with('success', trans('klusbib::admin/reservations/message.update.success'));
        }
        return redirect()->back()->withInput()->withErrors($reservation->getErrors());
    }
    /**
     * Returns a form with existing reservation data to allow an admin to
     * update reservation information.
     *
     * @param int $reservationId
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($reservationId = null)
    {
        if (is_null($item = Reservation::find($reservationId))) {
            return redirect()->route('klusbib.reservations.index')->with('error', trans('klusbib::admin/reservations/message.does_not_exist'));
        }
        $this->authorize('update', $item);

        $item->id = $item->reservation_id; // view expects id to be set to distinguish between create (POST) and update (PUT)
        return view('klusbib::reservations/edit', compact('item'));
    }


    /**
     * Validates and stores the reservation form data submitted from the edit
     * reservation form.
     *
     * @see ReservationsController::getEdit() method that provides the form view
     * @param Request $request
     * @param int $reservationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $reservationId = null)
    {
        if (is_null($reservation = Reservation::find($reservationId))) {
            return redirect()->route('klusbib.reservations.index')->with('error', trans('klusbib::admin/reservations/message.does_not_exist'));
        }
        Log::info('Reservation exists: ' . $reservation->exists);
        $this->authorize('update', $reservation);
        // test validating error
//        return redirect()->back()->withInput()->withErrors(new MessageBag(array ("state" => "Invalid state - test")) );

        $reservation->start_date   = $request->input('start_date');
        $reservation->end_date          = $request->input('end_date');
//        $reservation->name              = $request->input('name');
        $reservation->comment           = $request->input('notes');
        $reservation->tool_id           = $request->input('tool_id');
        $reservation->state             = $request->input('state');
        $reservation->user_id           = $request->input('user_id');
        $reservation->cancel_reason     = $request->input('cancel_reason');
        Log::info('Reservation: ' . \json_encode($reservation));

        if ($reservation->save()) {
            return redirect()->route('klusbib.reservations.show', ['reservation' => $reservationId])
                ->with('success', trans('klusbib::admin/reservations/message.update.success'));
        }

        // TODO: for "Bad Request" errors: parse the error message to identify erroneous field and return appropriate error array (field name is key in error array)
        // Note: getErrors returns validation errors linked to specific field (field name is key in error array)
        // From doc: An inherited member from a base class is overridden by a member inserted by a Trait.
        // The precedence order is that members from the current class override Trait methods, which in turn override inherited methods.
        // How to get API client errors?
        Log::info('Reservation update errors: ' . $reservation->getErrorCode() . \json_encode($reservation->getClientError())
            . \json_encode($reservation->getErrors()));

        // Add error message to Session, it will be shown by notifications.blade.php (included in layouts/default.blade.php)
        // Note: for Snipe models (e.g. License), the error is flashed to the session by the event handler (defined in model boot method e.g. License.php:boot)
//        $request->session()->flash('error', \json_encode($reservation->getClientError()));
//        return redirect()->back()->withInput()->withErrors($reservation->getErrors());

        $errorMessage = trans('klusbib::admin/reservations/message.update.error');
        $errors = Arr::get($reservation->getClientError(), 'errors');
        if (is_array($errors)) {
            $errorMessage .= " (API fout: ";
            foreach($errors as $key => $value) {
                if (\is_string($key)) {
                    $errorMessage .= $key . ": ";
                }
                $errorMessage .= $value;
            }
            $errorMessage .= ")";
        }
        // Show generic failure message
        return redirect()->route('klusbib.reservations.edit', ['reservation' => $reservationId])
            ->with('error', $errorMessage);
    }

    /**
     * Checks to see whether the selected reservation can be deleted, and
     * if it can, marks it as deleted.
     *
     * @param int $reservationId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($reservationId)
    {
        // Check if the reservation exists
        if (is_null($reservation = Reservation::find($reservationId))) {
            // Redirect to the reservation management page
            return redirect()->route('klusbib.reservations.index')
                ->with('error', trans('klusbib::admin/reservations/message.not_found'));
        }

        $this->authorize('delete', $reservation);

        if ( $reservation->delete()) {

            // Redirect to the reservations management page
            return redirect()->route('klusbib.reservations.index')
                ->with('success', trans('klusbib::admin/reservations/message.delete.success'));
        }
        // There are still reservations in use.
        return redirect()->route('klusbib.reservations.index')
            ->with('error', trans('klusbib::admin/reservations/message.delete.error'));

    }
    /**
     * Makes the reservation detail page.
     *
     * @param int $reservationId
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id = null)
    {

        $reservation = Reservation::find($id);

        if ($reservation) {
//            $this->authorize('view', $reservation);
            $reservation->id = $reservation->reservation_id;
            Log::info(\json_encode(compact('reservation')));
            return view('klusbib::reservations/view', compact('reservation'));
        }
        return redirect()->route('klusbib.reservations.index')
            ->with('error', trans('klusbib::admin/reservations/message.does_not_exist', compact('id')));
    }

}