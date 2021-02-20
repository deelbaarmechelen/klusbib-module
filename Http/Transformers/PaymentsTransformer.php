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

class PaymentsTransformer
{

    public function transformPayments ($payments, $total)
    {
        $array = array();
        foreach ($payments as $payment) {
            $array[] = self::transformPayment($payment);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformPayment ($payment)
    {
        Log::debug('payment = ' . \json_encode($payment));
        $array = [
            'id' => (int) $payment->payment_id,
            'user' => ($payment->user) ? [
                'id' => (int) $payment->user_id,
                'name'=> e($payment->user->first_name) .' '.e($payment->user->last_name),
            ] : null,
            'user_id' => e($payment->user_id),
            'state' => e($payment->state),
            'mode' => e($payment->mode),
            'paymentDate' => Helper::getFormattedDateObject($payment->payment_date, 'date'),
            'order_id' => ($payment->order_id) ? e($payment->order_id) : null,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'comment' => $payment->comment,
            'payment_ext_id' => $payment->payment_ext_id,
            'expiration_date' => Helper::getFormattedDateObject($payment->expiration_date, 'date'),
            // membership_id
            // loan_id
            'created_at' => (isset($payment->created_at) && isset($payment->created_at->date)) ? Helper::getFormattedDateObject($payment->created_at->date, 'datetime') : null,
            'updated_at' => (isset($payment->updated_at) && isset($payment->updated_at->date)) ? Helper::getFormattedDateObject($payment->updated_at->date, 'datetime') : null,
            ];

        $permissions_array['available_actions'] = [
//            'update' => (($payment->deleted_at==''))  ? true : false,
//            'cancel' => (($payment->deleted_at=='')) && ($payment->state != 'CANCELLED') ? true : false,
//            'confirm' => (($payment->deleted_at=='')) && ($payment->state == 'REQUESTED') ? true : false,
//            'delete' => (($payment->deleted_at=='')) ? true : false,
        ];

        $array += $permissions_array;

        return $array;
    }

    public function transformPaymentsDatatable($payments) {
        return (new DatatablesTransformer)->transformDatatables($payments);
    }

}
