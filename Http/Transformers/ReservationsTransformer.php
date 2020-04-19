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

class ReservationsTransformer
{

    public function transformReservations ($reservations, $total)
    {
        $array = array();
        foreach ($reservations as $reservation) {
            $array[] = self::transformReservation($reservation);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformReservation ($reservation)
    {
//        Log::debug('reservation = ' . \json_encode($reservation));
        $array = [
            'id' => (int) $reservation->reservation_id,
            'state' => e($reservation->state),
            'tool_id' => e($reservation->tool_id),
            'tool' => ($reservation->tool) ? [
                'id' => (int) $reservation->tool->id,
                'name'=> e($reservation->tool->asset_tag)
            ] : null,
            'user' => ($reservation->user_id) ? [
                'id' => (int) $reservation->user_id,
                'name'=> e($reservation->user->first_name) .' '.e($reservation->last_name),
//                'full_name'=> e($reservation->firstname).' '.e($reservation->lastname)
            ] : null,
            'user_id' => e($reservation->user_id),
            'title' => e($reservation->title),
            'startsAt' => Helper::getFormattedDateObject($reservation->startsAt, 'date'),
            'endsAt' => Helper::getFormattedDateObject($reservation->endsAt, 'date'),
            'comment' => ($reservation->comment) ? e($reservation->comment) : null,
            'created_at' => (isset($reservation->created_at) && isset($reservation->created_at->date)) ? Helper::getFormattedDateObject($reservation->created_at->date, 'datetime') : null,
            'updated_at' => (isset($reservation->updated_at) && isset($reservation->updated_at->date)) ? Helper::getFormattedDateObject($reservation->updated_at->date, 'datetime') : null,
            ];

        $permissions_array['available_actions'] = [
            'update' => (($reservation->deleted_at==''))  ? true : false,
            'cancel' => (($reservation->deleted_at=='')) && ($reservation->state != 'CANCELLED') ? true : false,
            'confirm' => (($reservation->deleted_at=='')) && ($reservation->state == 'REQUESTED') ? true : false,
            'delete' => (($reservation->deleted_at=='')) ? true : false,
//            'update' => (Gate::allows('update', Reservation::class) && ($reservation->deleted_at==''))  ? true : false,
//            'cancel' => (Gate::allows('update', Reservation::class) && ($reservation->deleted_at=='')) && ($reservation->state != 'CANCELLED') ? true : false,
//            'confirm' => (Gate::allows('update', Reservation::class) && ($reservation->deleted_at=='')) && ($reservation->state == 'REQUESTED') ? true : false,
//            'delete' => (Gate::allows('delete', Reservation::class) && ($reservation->deleted_at=='')) ? true : false,
//            'clone' => (Gate::allows('create', Reservation::class) && ($reservation->deleted_at=='')) ,
//            'restore' => (Gate::allows('create', Reservation::class) && ($reservation->deleted_at!='')) ? true : false,
        ];

        $array += $permissions_array;

        return $array;
    }

    public function transformReservationsDatatable($reservations) {
        return (new DatatablesTransformer)->transformDatatables($reservations);
    }





}
