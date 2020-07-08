<?php
namespace Modules\Klusbib\Http\Transformers;

use App\Http\Transformers\DatatablesTransformer;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
//use Modules\Klusbib\Models\Api\Reservation;
use phpDocumentor\Reflection\Types\Integer;
use Gate;
use App\Helpers\Helper;

class LendingsTransformer
{

    public function transformLendings ($lendings, $total)
    {
        $array = array();
        foreach ($lendings as $lending) {
            $array[] = self::transformLending($lending);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformLending ($lending)
    {
//        Log::debug('lending = ' . \json_encode($lending));
        $array = [
            'id' => (int) $lending->lending_id,
//            'state' => e($lending->state),
            'tool_id' => e($lending->tool_id),
            'tool' => ($lending->tool) ? [
                'id' => (int) $lending->tool->id,
                'name'=> e($lending->tool->asset_tag),
                'type' => e($lending->tool_type)
            ] : null,
            'user' => ($lending->user_id) ? [
                'id' => (int) $lending->user_id,
                'name'=> e($lending->user->first_name) .' '.e($lending->user->last_name),
//                'full_name'=> e($reservation->firstname).' '.e($reservation->lastname)
            ] : null,
            'user_id' => e($lending->user_id),
            'title' => e($lending->title),
            'startsAt' => Helper::getFormattedDateObject($lending->start_date, 'date'),
            'dueAt' => Helper::getFormattedDateObject($lending->due_date, 'date'),
            'returnedAt' => Helper::getFormattedDateObject($lending->returned_date, 'date'),
            'comment' => ($lending->comments) ? e($lending->comments) : null,
            'createdBy' => ($lending->created_by) ? e($lending->created_by) : null,
            'created_at' => (isset($lending->created_at) && isset($lending->created_at->date)) ? Helper::getFormattedDateObject($lending->created_at->date, 'datetime') : null,
            'updated_at' => (isset($lending->updated_at) && isset($lending->updated_at->date)) ? Helper::getFormattedDateObject($lending->updated_at->date, 'datetime') : null,
            ];

        $permissions_array['available_actions'] = [
//            'update' => (($lending->deleted_at==''))  ? true : false,
//            'cancel' => (($lending->deleted_at=='')) && ($lending->state != 'CANCELLED') ? true : false,
//            'confirm' => (($lending->deleted_at=='')) && ($lending->state == 'REQUESTED') ? true : false,
//            'delete' => (($lending->deleted_at=='')) ? true : false,
        ];

        $array += $permissions_array;

        return $array;
    }

    public function transformLendingsDatatable($lendings) {
        return (new DatatablesTransformer)->transformDatatables($lendings);
    }

}
