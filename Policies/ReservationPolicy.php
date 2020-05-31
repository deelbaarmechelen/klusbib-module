<?php

namespace Modules\Klusbib\Policies;

use App\Models\User;
use App\Policies\SnipePermissionsPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Klusbib\Models\Api\Reservation;

class ReservationPolicy extends SnipePermissionsPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
//    /**
//     * Determine if the given reservation can be updated by the user.
//     *
//     * @param  \App\Models\User  $user
//     * @param  \Modules\Klusbib\Models\Api\Reservation
//     * @return bool
//     */
//    public function update(User $user, Reservation $reservation)
//    {
//        return true;
////        return $user->id === $post->user_id;
//    }

    /**
     * This should return the key of the model in the users json permission string.
     *
     * @return boolean
     */
    protected function columnName()
    {
        return 'klusbib.reservations';
    }
}
