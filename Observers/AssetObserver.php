<?php

namespace Modules\Klusbib\Observers;

use App\Models\Asset;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Actionlog;
use Auth;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Models\AssetTagPattern;

class AssetObserver extends \App\Observers\AssetObserver
{


    /**
     * Listen to the Asset created event, and increment 
     * the next_auto_id value for corresponding asset tag pattern
     *
     * @param  Asset  $asset
     * @return void
     */
    public function created(Asset $asset)
    {
        $pattern = substr($asset->asset_tag, 0, -3) .  '000'; // replace 3 last digits by 000
        Log::debug('Asset created with pattern ' .$pattern);

        $assetTagPattern = AssetTagPattern::query()->where('pattern', '=', $pattern)->first();
        if (!isset($assetTagPattern)) {
            $assetTagPattern = new AssetTagPattern();
            $assetTagPattern->pattern = $pattern;
            $assetTagPattern->next_auto_id = 1;
            $assetTagPattern->save();
        }

        $assetTagSeqNbr = substr($asset->asset_tag, -3); // 3 last digits
        Log::debug("Current asset seq value " . intval($assetTagSeqNbr));
        $nextAutoId = intval($assetTagSeqNbr) + 1;
        if ($nextAutoId > $assetTagPattern->next_auto_id) {
            Log::debug('Updating next value for pattern ' . $pattern . ' to ' . $nextAutoId);
            $assetTagPattern->next_auto_id = $nextAutoId;
            $assetTagPattern->save();
        }
    }

}
