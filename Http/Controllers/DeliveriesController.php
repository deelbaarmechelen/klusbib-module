<?php

namespace Modules\Klusbib\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\KlusbibApi;
use Modules\Klusbib\Http\Transformers\InventoryItemsTransformer;
use Modules\Klusbib\Models\Api\Delivery;

class DeliveriesController extends Controller
{
    /**
     * Returns a view that invokes the ajax tables which actually contains
     * the content for the deliveries listing, which is generated in getDatatable().
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
//        $deliveries = \Modules\Klusbib\Models\Api\Delivery::all();
        return view('klusbib::deliveries/index');
    }

    /**
     * Returns a form view that allows an admin to create a new licence.
     *
     * @see AccessoriesController::getDatatable() method that generates the JSON response
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->authorize('create', Delivery::class);

        return view('klusbib::deliveries/edit')
            //->with('delivery_options',$delivery_options)
            ->with('item', new Delivery());

    }

    /**
     * Validates and stores the delivery form data submitted from the new
     * delivery form.
     *
     * @see DeliverysController::create() method that provides the form view
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Delivery::class);

        // create a new model instance
        $delivery = new Delivery();
        // Save the delivery data
        $delivery->pick_up_date      = $request->input('pick_up_date');
        $delivery->pick_up_address   = $request->input('pick_up_address');
        $delivery->drop_off_date     = $request->input('drop_off_date');
        $delivery->drop_off_address  = $request->input('drop_off_address');
        $delivery->comment           = $request->input('notes');
        $delivery->tool_id           = $request->input('tool_id');
        $delivery->state             = $request->input('state');
        $delivery->user_id           = $request->input('user_id');
        $delivery->type              = $request->input('type');
        $delivery->price             = $request->input('price');
        $delivery->consumers         = $request->input('consumers');
        $delivery->payment_id        = $request->input('payment_id');
        $delivery->contact_id        = $request->input('contact_id');
        Log::info('Delivery: ' . \json_encode($delivery));
//        $delivery->user_id           = Auth::id();

        if ($delivery->save()) {
            return redirect()->route("klusbib.deliveries.index")->with('success', trans('klusbib::admin/deliveries/message.create.success'));
        }
//        return redirect()->back()->withInput()->withErrors($delivery->getErrors());
        $errorMessage = trans('klusbib::admin/deliveries/message.create.error');
        $errorMessage .= $this->formatApiErrorMessage($delivery->getClientError());
        // Show generic failure message
        return redirect()->back()->withInput()
            ->with('error', $errorMessage);
    }

    public function cancel($deliveryId = null)
    {
        $this->authorize('update', Delivery::class);
//        if (is_null($item = Delivery::find($deliveryId))) {
//            return redirect()->route('klusbib.deliveries.index')->with('error', trans('klusbib::admin/deliveries/message.does_not_exist'));
//        }
        $delivery = Delivery::find($deliveryId);
        if (is_null($delivery)) {
            return redirect()->route('klusbib.deliveries.index')->with('error', trans('klusbib::admin/deliveries/message.does_not_exist'));
        }
//        $item->id = $item->delivery_id; // view expects id to be set to distinguish between create (POST) and update (PUT)
//        $item->state = 'CANCELLED';
//        return view('klusbib::deliveries/edit', compact('item'));

        $delivery->state = 'CANCELLED';
        if ($delivery->save()) {
            return redirect()->route("klusbib.deliveries.index")->with('success', trans('klusbib::admin/deliveries/message.update.success'));
        }
        return redirect()->back()->withInput()->withErrors($delivery->getErrors());
    }
    public function confirm($deliveryId = null)
    {

        $delivery = Delivery::find($deliveryId);
        $this->authorize('confirm', $delivery);
        if (is_null($delivery)) {
            return redirect()->route('klusbib.deliveries.index')->with('error', trans('klusbib::admin/deliveries/message.does_not_exist'));
        }
        if (is_null($delivery->price)) {
            return redirect()->route('klusbib.deliveries.index')->with('error', trans('klusbib::admin/deliveries/message.price_missing'));
        }
        if ($delivery->type == "PICKUP" && (is_null($delivery->pick_up_date) || is_null($delivery->pick_up_address))
         || $delivery->type == "DROPOFF" && (is_null($delivery->drop_off_date) || is_null($delivery->drop_off_address)) ) {
            return redirect()->route('klusbib.deliveries.index')->with('error', trans('klusbib::admin/deliveries/message.date_or_address_missing'));
        }

        $delivery->state = 'CONFIRMED';
        if ($delivery->save()) {
            return redirect()->route("klusbib.deliveries.index")->with('success', trans('klusbib::admin/deliveries/message.update.success'));
        }
        return redirect()->back()->withInput()->withErrors($delivery->getErrors());
    }
    /**
     * Returns a form with existing delivery data to allow an admin to
     * update delivery information.
     *
     * @param int $deliveryId
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($deliveryId = null)
    {
        if (is_null($item = Delivery::find($deliveryId))) {
            return redirect()->route('klusbib.deliveries.index')->with('error', trans('klusbib::admin/deliveries/message.does_not_exist'));
        }
        $this->authorize('update', $item);
        Log::info('Delivery item found' . \json_encode(compact('item')) );

        return view('klusbib::deliveries/edit')
            ->with('item', $item)
            ->with('allowed_delivery_states', $this->getAllowedDeliveryStates());
    }

    /**
     * @return array of allowed delivery states
     */
    private function getAllowedDeliveryStates(): array
    {
        $allowed_delivery_states = array(
            array("value" => "REQUESTED", "enabled" => true),
            array("value" => "CONFIRMED", "enabled" => Gate::allows('confirm', Delivery::class)),
            array("value" => "DELIVERED", "enabled" => true),
            array("value" => "CANCELLED", "enabled" => true),
        );
        return $allowed_delivery_states;
    }
    /**
     * Validates and stores the delivery form data submitted from the edit
     * delivery form.
     *
     * @see DeliverysController::getEdit() method that provides the form view
     * @param Request $request
     * @param int $deliveryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $deliveryId = null)
    {
        if (is_null($delivery = Delivery::find($deliveryId))) {
            return redirect()->route('klusbib.deliveries.index')->with('error', trans('klusbib::admin/deliveries/message.does_not_exist'));
        }
        Log::info('Delivery exists: ' . $delivery->exists);
        $this->authorize('update', $delivery);
        if ($request->input('state') == 'CONFIRMED' && $delivery->state != 'CONFIRMED') {
            Log::info('Delivery confirmation: ' . \json_encode($delivery));
            $this->authorize('confirm', $delivery);
        }
        $delivery->state             = $request->input('state');

        // test validating error
//        return redirect()->back()->withInput()->withErrors(new MessageBag(array ("state" => "Invalid state - test")) );

        $delivery->pick_up_date      = $request->input('pick_up_date');
        $delivery->pick_up_address      = $request->input('pick_up_address');
        $delivery->drop_off_date     = $request->input('drop_off_date');
        $delivery->drop_off_address     = $request->input('drop_off_address');
        $delivery->comment           = $request->input('notes');
//        $delivery->tool_id           = $request->input('tool_id');
        $delivery->state             = $request->input('state');
        $delivery->user_id           = $request->input('user_id');
        $delivery->type              = $request->input('type');
        $delivery->price             = $request->input('price');
        $delivery->consumers         = $request->input('consumers');
        $delivery->payment_id        = $request->input('payment_id');
        $delivery->contact_id        = $request->input('contact_id');
        Log::info('Delivery: ' . \json_encode($delivery));

        if ($delivery->save()) {
            return redirect()->route('klusbib.deliveries.show', ['delivery' => $deliveryId])
                ->with('success', trans('klusbib::admin/deliveries/message.update.success'));
        }

        // TODO: for "Bad Request" errors: parse the error message to identify erroneous field and return appropriate error array (field name is key in error array)
        // Note: getErrors returns validation errors linked to specific field (field name is key in error array)
        // From doc: An inherited member from a base class is overridden by a member inserted by a Trait.
        // The precedence order is that members from the current class override Trait methods, which in turn override inherited methods.
        // How to get API client errors?
        Log::info('Delivery update errors: ' . $delivery->getErrorCode() . \json_encode($delivery->getClientError())
            . \json_encode($delivery->getErrors()));

        // Add error message to Session, it will be shown by notifications.blade.php (included in layouts/default.blade.php)
        // Note: for Snipe models (e.g. License), the error is flashed to the session by the event handler (defined in model boot method e.g. License.php:boot)
//        $request->session()->flash('error', \json_encode($delivery->getClientError()));
//        return redirect()->back()->withInput()->withErrors($delivery->getErrors());

        $errorMessage = trans('klusbib::admin/deliveries/message.update.error');
        $errorMessage .= $this->formatApiErrorMessage($delivery->getClientError());
        // Show generic failure message
        return redirect()->route('klusbib.deliveries.edit', ['delivery' => $deliveryId])
            ->with('error', $errorMessage);
    }

    /**
     * Checks to see whether the selected delivery can be deleted, and
     * if it can, marks it as deleted.
     *
     * @param int $deliveryId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($deliveryId)
    {
        // Check if the delivery exists
        if (is_null($delivery = Delivery::find($deliveryId))) {
            // Redirect to the delivery management page
            return redirect()->route('klusbib.deliveries.index')
                ->with('error', trans('klusbib::admin/deliveries/message.not_found'));
        }

        $this->authorize('delete', $delivery);

        if ( $delivery->delete()) {

            // Redirect to the deliveries management page
            return redirect()->route('klusbib.deliveries.index')
                ->with('success', trans('klusbib::admin/deliveries/message.delete.success'));
        }
        // There are still deliveries in use.
        return redirect()->route('klusbib.deliveries.index')
            ->with('error', trans('klusbib::admin/deliveries/message.delete.error'));

    }
    /**
     * Makes the delivery detail page.
     *
     * @param int $deliveryId
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id = null)
    {

        $delivery = Delivery::find($id);

        if ($delivery) {
            $this->authorize('view', $delivery);
            Log::info(\json_encode(compact('delivery')));
            $items = \json_decode($delivery->items);

            $transformedItems = (new InventoryItemsTransformer)->transformInventoryItems($items, count($items));
            $delivery->items = $transformedItems;
            Log::info(\json_encode(compact('delivery')));
            return view('klusbib::deliveries/view', compact('delivery'));
        }
        return redirect()->route('klusbib.deliveries.index')
            ->with('error', trans('klusbib::admin/deliveries/message.does_not_exist', compact('id')));
    }

    /**
     * Add an inventory item to delivery
     * @param int $deliveryId
     */
    public function newItem($deliveryId) {
        if (is_null($delivery = Delivery::find($deliveryId))) {
            // Redirect to the delivery management page
            return redirect()->route('klusbib.deliveries.index')
                ->with('error', trans('klusbib::admin/deliveries/message.not_found'));
        }
        $this->authorize('update', Delivery::class);

        return view('klusbib::deliveries/edititem')
            //->with('delivery_options',$delivery_options)
            ->with('delivery', $delivery)
            ->with('item', new Asset());

    }

    /**
     * Add an inventory item to delivery
     * @param Request $request
     * @param int $deliveryId
     */
    public function addItem(Request $request, $deliveryId) {
        if (is_null($delivery = Delivery::find($deliveryId))) {
            // Redirect to the delivery management page
            return redirect()->route('klusbib.deliveries.index')
                ->with('error', trans('klusbib::admin/deliveries/message.not_found'));
        }
        $this->authorize('update', Delivery::class);
        $itemId = $request->input('tool_id');
        if (Delivery::addInventoryItem($deliveryId, $itemId)) {

            // Redirect to the deliveries show page
            return redirect()->route('klusbib.deliveries.show', ['delivery' => $deliveryId])
                ->with('success', trans('klusbib::admin/deliveries/message.add_item.success'));

        }
        // There are still deliveries in use.
        return redirect()->route('klusbib.deliveries.index')
            ->with('error', trans('klusbib::admin/deliveries/message.add_item.error'));
    }
    /**
     * Edit inventory item in delivery
     * @param int $deliveryId
     * @param int $itemId
     */
    public function editItem($deliveryId, $itemId) {
        throw new \RuntimeException("Not yet implemented!");
    }
    /**
     * Remove inventory item from delivery
     * @param int $deliveryId
     * @param int $itemId
     */
    public function removeItem($deliveryId, $itemId) {
        // Check if the delivery exists
        if (is_null($delivery = Delivery::find($deliveryId))) {
            // Redirect to the delivery management page
            return redirect()->route('klusbib.deliveries.index')
                ->with('error', trans('klusbib::admin/deliveries/message.not_found'));
        }

        $this->authorize('delete', $delivery);

        if (Delivery::removeInventoryItem($deliveryId, $itemId)) {

            // Redirect to the deliveries show page
            return redirect()->route('klusbib.deliveries.show', ['delivery' => $deliveryId])
                ->with('success', trans('klusbib::admin/deliveries/message.remove_item.success'));

        }
        // There are still deliveries in use.
        return redirect()->route('klusbib.deliveries.index')
            ->with('error', trans('klusbib::admin/deliveries/message.remove_item.error'));

    }

    /**
     * @param $clientError MessageBag containing API client error(s)
     * @return string formatted error message to be appended to a general error message
     */
    private function formatApiErrorMessage($clientError): string
    {
        $errorMessage = "";
        $errors = Arr::get($clientError, 'errors');
        if (is_array($errors)) {
            $errorMessage .= " (API fout: ";
            foreach ($errors as $key => $value) {
                if (\is_string($key)) {
                    $errorMessage .= $key . ": ";
                }
                $errorMessage .= $value;
            }
            $errorMessage .= ")";
        }
        return $errorMessage;
    }

}