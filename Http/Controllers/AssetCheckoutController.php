<?php

namespace Modules\Klusbib\Http\Controllers;

use App\Exceptions\CheckoutNotAllowed;
use App\Models\Asset;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetCheckoutController
{
    use AuthorizesRequests;

    public function edit($assetId)
    {
        // Check if the asset exists
        if (is_null($asset = Asset::find(e($assetId)))) {
            return redirect()->route('hardware.index')->with('error', trans('admin/hardware/message.does_not_exist'));
        }

        $this->authorize('checkout', $asset);

        return view('klusbib::hardware.extend', compact('asset'));
        // TODO: add checkout_at and expected_checkin

    }

    public function update(Request $request, $assetId)
    {
        try {
            // Check if the asset exists
            if (!$asset = Asset::find($assetId)) {
                return redirect()->route('hardware.index')->with('error', trans('admin/hardware/message.does_not_exist'));
            } elseif (!$this->assetCheckedOut($asset)) {
                return redirect()->route('hardware.index')->with('error', trans('admin/hardware/message.extend.not_available'));
            }
            $this->authorize('checkout', $asset);

            $expected_checkin = $asset->expected_checkin;
            if ($request->filled('expected_checkin')) {
                $expected_checkin = $request->get('expected_checkin');
            }

            if ($this->updateExpectedCheckin($asset, $expected_checkin, e($request->get('note')) )) {
                return redirect()->route("hardware.index")->with('success', trans('klusbib::admin/hardware/message.extend.success'));
            }

            // Redirect to the asset management page with error
            return redirect()->to("hardware/$assetId/extend")->with('error', trans('klusbib::admin/hardware/message.extend.error'))->withErrors($asset->getErrors());
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', trans('klusbib::admin/hardware/message.extend.error'))->withErrors($asset->getErrors());
        } catch (CheckoutNotAllowed $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function assetCheckedOut(Asset $asset) {
        return
            (!empty($asset->assigned_to)) &&
            (empty($asset->deleted_at)) &&
            (($asset->assetstatus) && ($asset->assetstatus->deployable == 1));
    }

    private function updateExpectedCheckin(Asset $asset, $expected_checkin, $note) {
        $asset->expected_checkin = $expected_checkin;
        if ($asset->save()) {
            //$asset->logCheckout($note, $target);
            return true;
        }
        return false;
    }
}