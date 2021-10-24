<?php

namespace Modules\Klusbib\Http\Controllers;

use App\Models\Asset;
use App\Models\Company;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class AssetsController
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('index', Asset::class);
        if ($request->filled('company_id')) {
            $company = Company::find($request->input('company_id'));
        } else {
            $company = null;
        }
        return view('klusbib::hardware/index')->with('company', $company);
    }

    public function extend(Request $request) {
        return view('klusbib::hardware/extend')->with('company', $company);
    }

    /**
     * Return a 2D barcode for the asset
     * Copy of Snipe IT AssetsController::getBarCode, customizing the barcode type and sizes to match our scanner needs
     *
     * @param int $assetId
     * @return Response
     */
    public function getBarCode($assetId = null)
    {
        $settings = Setting::getSettings();
        $asset = Asset::find($assetId);
        $barcode_file = public_path().'/uploads/barcodes/'.str_slug($settings->alt_barcode).'-'.str_slug($asset->asset_tag).'.png';

        if (isset($asset->id, $asset->asset_tag)) {
            if (file_exists($barcode_file)) {
                $header = ['Content-type' => 'image/png'];
                return response()->file($barcode_file, $header);
            } else {
                // Calculate barcode width in pixel based on label width (inch)
                $barcode_width = ($settings->labels_width - $settings->labels_display_sgutter) * 96.000000000001;

                $barcode = new \Com\Tecnick\Barcode\Barcode();
                $barcode_obj = $barcode->getBarcodeObj($settings->alt_barcode, $asset->asset_tag, -3, 100, 'black', array(-0.2, -0.2, -0.2, -0.2));
                //$barcode_obj = $barcode->getBarcodeObj($settings->alt_barcode,$asset->asset_tag,($barcode_width < 200 ? $barcode_width : 200),75);

                file_put_contents($barcode_file, $barcode_obj->getPngData());
                return response($barcode_obj->getPngData())->header('Content-type', 'image/png');
            }
        }
    }

}