<?php

namespace Modules\Klusbib\Policies;

use App\Policies\SnipePermissionsPolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy extends SnipePermissionsPolicy
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
        return 'klusbib.payments';
    }
}
