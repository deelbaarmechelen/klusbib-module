<?php
namespace Modules\Klusbib\Http\Transformers;

use App\Http\Transformers\DatatablesTransformer;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
//use Modules\Klusbib\Models\Api\Reservation;
use phpDocumentor\Reflection\Types\Integer;
use Gate;
use App\Helpers\Helper;

class MembershipsTransformer
{

    public function transformMemberships ($memberships, $total)
    {
        $array = array();
        foreach ($memberships as $membership) {
            $array[] = self::transformMembership($membership);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformMembership ($membership)
    {
        Log::debug('membership = ' . \json_encode($membership));
        $subscription = \json_decode($membership->subscription,false); // subscription is received as string -> transform it to object
        $array = [
            'id' => (int) $membership->id,
            // Note: API user is transformed by controller into snipe user
            'user' => (isset($membership->user)) ? [
                'id' => (int) $membership->user->id,
                'name'=> e($membership->user->first_name) .' '.e($membership->user->last_name),
            ] : null,
            'user_id' => e($membership->contact_id),
            'state' => e($membership->status),
            'subscription_id' => (is_object($subscription)) ? (int) ($subscription->id) : null,
            'subscription' => (is_object($subscription)) ? [
                'id' => (int) ($subscription->id),
                'name'=> __('klusbib::types/membershiptypes.' . strtoupper(e($subscription->name)), array(), 'nl'),
                'price' => e($subscription->price),
                'duration' => e($subscription->duration),
            ] : null,
            'start_at' => Helper::getFormattedDateObject($membership->start_at, 'date'),
            'expires_at' => Helper::getFormattedDateObject($membership->expires_at, 'date'),
            'last_payment_mode' => ($membership->last_payment_mode) ?
                __('klusbib::types/paymentmodes.' . strtoupper(e($membership->last_payment_mode)), array(), 'nl')
                : null,
            'comment' => ($membership->comment) ? e($membership->comment) : null,
//            'created_at' => (isset($membership->created_at) && isset($membership->created_at->date)) ? Helper::getFormattedDateObject($membership->created_at->date, 'datetime') : null,
//            'updated_at' => (isset($membership->updated_at) && isset($membership->updated_at->date)) ? Helper::getFormattedDateObject($membership->updated_at->date, 'datetime') : null,
            ];

        $permissions_array['available_actions'] = [
//            'update' => (($membership->deleted_at==''))  ? true : false,
//            'cancel' => (($membership->deleted_at=='')) && ($membership->state != 'CANCELLED') ? true : false,
//            'confirm' => (($membership->deleted_at=='')) && ($membership->state == 'REQUESTED') ? true : false,
//            'delete' => (($membership->deleted_at=='')) ? true : false,
        ];

        $array += $permissions_array;

        return $array;
    }

    public function transformMembershipsDatatable($memberships) {
        return (new DatatablesTransformer)->transformDatatables($memberships);
    }

}
