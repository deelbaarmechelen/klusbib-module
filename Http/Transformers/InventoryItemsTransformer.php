<?php
namespace Modules\Klusbib\Http\Transformers;

use App\Http\Transformers\DatatablesTransformer;
use App\Models\Delivery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
//use Modules\Klusbib\Models\Api\Delivery;
use phpDocumentor\Reflection\Types\Integer;
use Gate;
use App\Helpers\Helper;

class InventoryItemsTransformer
{

    public function transformInventoryItems ($deliveries, $total)
    {
        $array = array();
        foreach ($deliveries as $delivery) {
            $array[] = self::transformInventoryItem($delivery);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformInventoryItem ($item)
    {
// Inventory item = {"delivery_id":1,"inventory_item_id":1,"id":1,"item_type":"TOOL","sku":"KB-000-20-001","name":"Bouwstofzuiger","description":null,"keywords":"general","brand":"Makita","is_active":1,"show_on_website":1}
        Log::debug('Inventory item = ' . \json_encode($item));
        $array = [
            'id' => (int) $item->id,
            'item_type' => e($item->item_type),
            'sku' => e($item->sku),
            'name' => e($item->name),
            'description' => e($item->description),
            'keywords' => e($item->keywords),
            'brand' => e($item->brand),
            'is_active' => e($item->is_active),
            'show_on_website' => e($item->show_on_website),
//            'created_at' => (isset($item->created_at) && isset($item->created_at->date)) ? Helper::getFormattedDateObject($item->created_at->date, 'datetime') : null,
//            'updated_at' => (isset($item->updated_at) && isset($item->updated_at->date)) ? Helper::getFormattedDateObject($item->updated_at->date, 'datetime') : null,
            ];

        $permissions_array['available_actions'] = [
            'update' => false, // nothing to be updated yet
            'delete' => true,
//            'update' => (Gate::allows('update', Delivery::class) && ($delivery->deleted_at==''))  ? true : false,
//            'cancel' => (Gate::allows('update', Delivery::class) && ($delivery->deleted_at=='')) && ($delivery->state != 'CANCELLED') ? true : false,
//            'confirm' => (Gate::allows('update', Delivery::class) && ($delivery->deleted_at=='')) && ($delivery->state == 'REQUESTED') ? true : false,
//            'delete' => (Gate::allows('delete', Delivery::class) && ($delivery->deleted_at=='')) ? true : false,
//            'clone' => (Gate::allows('create', Delivery::class) && ($delivery->deleted_at=='')) ,
//            'restore' => (Gate::allows('create', Delivery::class) && ($delivery->deleted_at!='')) ? true : false,
        ];

        $array += $permissions_array;

        return $array;
    }

    public function transformInventoryItemsDatatable($items) {
        return (new DatatablesTransformer)->transformDatatables($items);
    }

}
