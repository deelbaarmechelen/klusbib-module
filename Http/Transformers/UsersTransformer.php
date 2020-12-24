<?php
namespace Modules\Klusbib\Http\Transformers;

use App\Http\Transformers\DatatablesTransformer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Integer;
use Gate;
use App\Helpers\Helper;

class UsersTransformer
{

    public function transformUsers ($users, $total)
    {
        $array = array();
        foreach ($users as $user) {
            $array[] = self::transformUser($user);
        }
        return (new DatatablesTransformer)->transformDatatables($array, $total);
    }

    public function transformUser ($user)
    {
//        Log::debug('user = ' . \json_encode($user));
        $array = [
            'id' => (int) $user->user_ext_id,
            'user_id' => (int) $user->user_id,
            'state' => e($user->state),
            'name' => e($user->firstname).' '.e($user->lastname),
            'firstname' => e( $user->firstname),
            'lastname' => e( $user->lastname),
            'email' => e($user->email),
            'email_state' => __('klusbib::types/emailstates.' . strtoupper(e($user->email_state)), array(), 'nl'),
            'role' => e($user->role),
            'membership_start_date' => Helper::getFormattedDateObject($user->membership_start_date, 'date'),
            'membership_end_date' => Helper::getFormattedDateObject($user->membership_end_date, 'date'),
            'address' => ($user->address) ? e($user->address) : null,
            'postal_code' => ($user->postal_code) ? e($user->postal_code) : null,
            'city' => ($user->city) ? e($user->city) : null,
            'phone' => ($user->phone) ? e($user->phone) : null,
            'mobile' => ($user->mobile) ? e($user->mobile) : null,
            'registration_number' => (int) $user->registration_number,
            'payment_mode' => ($user->payment_mode) ?
                __('klusbib::types/paymentmodes.' . strtoupper(e($user->payment_mode)), array(), 'nl')
                : null,
            'accept_terms_date' => Helper::getFormattedDateObject($user->accept_terms_date, 'date'),
            'created_at' => (isset($user->created_at) && isset($user->created_at->date)) ? Helper::getFormattedDateObject($user->created_at->date, 'datetime') : null,
            'updated_at' => (isset($user->updated_at) && isset($user->updated_at->date)) ? Helper::getFormattedDateObject($user->updated_at->date, 'datetime') : null,
            ];

        $permissions_array['available_actions'] = [
            'show' => ($user->deleted_at=='') ? true : false,
            'update' => ($user->deleted_at=='') ? true : false,
//            'show' => (Gate::allows('show', User::class) && ($user->deleted_at==''))  ? true : false,
//            'update' => (Gate::allows('update', User::class) && ($user->deleted_at==''))  ? true : false,
//            'delete' => (Gate::allows('delete', User::class) && ($user->deleted_at=='') && ($user->assets_count == 0) && ($user->licenses_count == 0)  && ($user->accessories_count == 0)  && ($user->consumables_count == 0)) ? true : false,
//            'clone' => (Gate::allows('create', User::class) && ($user->deleted_at=='')) ,
//            'restore' => (Gate::allows('create', User::class) && ($user->deleted_at!='')) ? true : false,
        ];

        $array += $permissions_array;

        return $array;
    }
    public function transformSyncedUser ($user) {
        if ($user->avatar == \Modules\Klusbib\Models\Api\User::STATE_ACTIVE_AVATAR) {
            $state = 'ACTIVE';
        } else {
            $state = 'INACTIVE';
        }
        $array = [
            'id' => (int) $user->id,
            'employee_num' => (int) $user->employee_num,
            'state' => $state,
            'first_name' => e( $user->first_name),
            'last_name' => e( $user->last_name),
            'username' => e($user->username),
            ];
        return $array;
    }
    public function transformUsersDatatable($users) {
        return (new DatatablesTransformer)->transformDatatables($users);
    }





}
