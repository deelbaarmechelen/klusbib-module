<?php

namespace Modules\Klusbib\Models\Api;

use Illuminate\Support\Facades\Log;
use Torann\RemoteModel\Model as BaseModel;
use Watson\Validating\ValidatingTrait;

class Delivery extends BaseModel
{
    // model validation: https://github.com/dwightwatson/validating
    use ValidatingTrait;

    protected $rules = array(
        'asset_id' => 'required|exists:users',
        'user_id' => 'required',
    );

    /**
     * Add an inventory item to delivery.
     *
     * @param  string $deliveryId
     * @param  string $inventoryItemId
     */
    public static function addInventoryItem($deliveryId, $inventoryItemId)
    {
        $instance = new static([], static::getParentID());

        try {
            $instance->request($instance->getEndpoint(), 'addItem', [$deliveryId, $inventoryItemId]);
            if ($instance->hasErrors()) {
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            Log::info("Delivery::addInventoryItem exception catched: " . $exception->getMessage());
            return false;
        }
    }

    /**
     * Remove an inventory item from delivery.
     *
     * @param  string $deliveryId
     * @param  string $inventoryItemId
     */
    public static function removeInventoryItem($deliveryId, $inventoryItemId)
    {
        $instance = new static([], static::getParentID());

        try {
            $instance->request($instance->getEndpoint(), 'removeItem', [$deliveryId, $inventoryItemId]);
            if ($instance->hasErrors()) {
                return false;
            }
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

}