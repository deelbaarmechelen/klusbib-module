<?php

namespace Modules\Klusbib\Listeners;

use App\Events\CheckoutableCheckedOut;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyApiAssetCheckout
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param CheckoutableCheckedOut $event
     * @return void
     */
    public function handle(CheckoutableCheckedOut $event)
    {
        //
    }
}
