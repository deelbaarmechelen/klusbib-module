<?php

namespace Modules\Klusbib\Policies;

use App\Models\User;
use App\Policies\SnipePermissionsPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class UserPolicy extends SnipePermissionsPolicy
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

    /**
     * This should return the key of the model in the users json permission string.
     *
     * @return boolean
     */
    protected function columnName()
    {
        return 'klusbib.users';
    }

   /* public function index(User $user)
    {
        Log::debug('Checking Klusbib User index policy');
        return true;
    }
    public function create(User $user)
    {
        Log::debug('Checking Klusbib User create policy');
        return true;
    }
*/
    /**
     * Determine if the given post can be updated by the user.
     *
     * @param  User  $user
     * @param  \Modules\Klusbib\Models\Api\User $apiUser
     * @return bool
     */
    /* Explicit method not needed as long as default implementation of parent SnipePermissionsPolicy fulfills requirements
    public function update(User $user, $apiUser = null)
    {
        return $user->id === $apiUser->user_id;
    }
    */
}
