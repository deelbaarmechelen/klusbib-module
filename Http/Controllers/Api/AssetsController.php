<?php

namespace Modules\Klusbib\Http\Controllers\Api;


use App\Models\Asset;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Http\Requests\AssetRequest;
use Modules\Klusbib\Models\AssetTagPattern;

class AssetsController extends \App\Http\Controllers\Api\AssetsController
{
    /**
     * Accepts a POST request to create a new asset
     *
     * @author [A. Gianotto] [<snipe@snipe.net>]
     * @param Request $request
     * @since [v4.0]
     * @return \Illuminate\Http\JsonResponse
     */
    public function customStore(AssetRequest $request)
    {
        $asset_tag = $request->get('asset_tag');
        if (!isset($asset_tag)) {
            $serial                  = $request->get('serial');
            $company_id              = Company::getIdForCurrentUser($request->get('company_id'));
            $companyPrefix = $this->getCompanyPrefix($company_id);
            // get next value for asset_tag
            $next_seq_id = 1;
            $asset_tag_pattern = $companyPrefix . '-' . date('y') . '-' . '000';
            $assetPattern = AssetTagPattern::query()->where('pattern', '=', $asset_tag_pattern)->first();
            if (isset($assetPattern)) {
                $next_seq_id = $assetPattern->next_auto_id;
            }
            $asset_tag = str_replace('000', Asset::zerofill($next_seq_id, 3), $asset_tag_pattern);
            $request->request->set('asset_tag', $asset_tag);
        }

        return parent::store($request);
    }
    
    private function getCompanyPrefix($companyId) {
        $company = Company::find((int) $companyId);
        if (strcasecmp($company->name ,"DIGIBIB") == 0) {
            return "DB";
        } else if (strcasecmp($company->name ,"KLUSBIB") == 0) {
            return "KB";
        }
        return "";
    }
}