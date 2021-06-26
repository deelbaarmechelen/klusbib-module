<?php
namespace Modules\Klusbib\Http\Transformers;

use App\Http\Transformers\DatatablesTransformer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Models\Api\Delivery;
use phpDocumentor\Reflection\Types\Integer;
use Gate;
use App\Helpers\Helper;

class DeliveriesTransformer
{

    public function transformDeliveries ($deliveries, $total)
    {
        $array = array();
        foreach ($deliveries as $delivery) {
            $array[] = self::transformDelivery($delivery);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformDelivery ($delivery)
    {
        Log::debug('delivery = ' . \json_encode($delivery));
        $array = [
            'id' => (int) $delivery->id,
            'state' => e($delivery->state),
            'type' => ($delivery->type) ? e($delivery->type) : null,
            'tool_id' => e($delivery->tool_id),
            'tool' => ($delivery->tool) ? [
                'id' => (int) $delivery->tool->id,
                'name'=> e($delivery->tool->asset_tag)
            ] : null,
            'user' => ($delivery->user_id) ? [
                'id' => (int) $delivery->user_id,
                'name'=> e($delivery->user->first_name) .' '.e($delivery->user->last_name),
//                'full_name'=> e($delivery->firstname).' '.e($delivery->lastname)
            ] : null,
            'user_id' => e($delivery->user_id),
            'pick_up_date' => Helper::getFormattedDateObject($delivery->pick_up_date, 'date'),
            'pick_up_address' => ($delivery->pick_up_address) ? e($delivery->pick_up_address) : null,
            'drop_off_date' => Helper::getFormattedDateObject($delivery->drop_off_date, 'date'),
            'drop_off_address' => ($delivery->drop_off_address) ? e($delivery->drop_off_address) : null,
            'price' => ($delivery->price) ? e($delivery->price) : null,
            'consumers' => ($delivery->consumers) ? e($delivery->consumers) : null,
            'comment' => ($delivery->comment) ? e($delivery->comment) : null,
            'created_at' => (isset($delivery->created_at) && isset($delivery->created_at->date)) ? Helper::getFormattedDateObject($delivery->created_at->date, 'datetime') : null,
            'updated_at' => (isset($delivery->updated_at) && isset($delivery->updated_at->date)) ? Helper::getFormattedDateObject($delivery->updated_at->date, 'datetime') : null,
            ];

        $permissions_array['available_actions'] = [
            'show' => ($delivery->deleted_at=='') ? true : false,
            'update' => (Gate::allows('update', Delivery::class) && ($delivery->deleted_at==''))  ? true : false,
            'cancel' => (Gate::allows('update', Delivery::class) && ($delivery->deleted_at=='') && ($delivery->state != 'CANCELLED')) ? true : false,
            'confirm' => (Gate::allows('confirm', Delivery::class) && ($delivery->deleted_at=='') && ($delivery->state == 'REQUESTED')) ? true : false,
            'delete' => ($delivery->deleted_at=='') ? true : false,
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

    public function transformDeliveriesDatatable($deliveries) {
        return (new DatatablesTransformer)->transformDatatables($deliveries);
    }





}
