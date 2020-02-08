<?php

namespace Modules\Klusbib\Notifications;

use App\Notifications\CheckoutAccessoryNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Channels\KlusbibChannel;
use Modules\Klusbib\Notifications\Messages\LendingApiMessage;

/**
 * Extension of CheckoutAccessoryNotification
 * Adds Klusbib specific extra notifications on top of the Snipe IT notifications
 *
 * @package Modules\Klusbib\Notifications
 */
class NotifyAccessoryCheckout extends CheckoutAccessoryNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        parent::__construct($params);
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $notifyBy = parent::via($notifiable);
        if (isset($this->target) && isset($this->target->employee_num)) {
            \Log::debug('use Klusbib');
            // Skip Klusbib notification if klusbib user id (=employee_num) is not set (not a Klusbib user)
            array_push($notifyBy, KlusbibChannel::class);
        }
        return $notifyBy;
    }

    /**
     * Get the klusbib api representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return LendingApiMessage
     */
    public function toKlusbib() {
        Log::debug("Notify Accessory checkout to Klusbib lending API");
        $target = $this->target;
        $admin = $this->admin;
        $item = $this->item;
        $note = $this->note;

        $message = (new LendingApiMessage(LendingApiMessage::METHOD_CREATE, $target, $item, $note, $admin))
            ->userId($target->employee_num)->tool($item->id, LendingApiMessage::TOOL_TYPE_ACCESSORY)
            ->startDate($this->last_checkout);

        if (($this->expected_checkin) && ($this->expected_checkin!='')) {
            $message = $message->dueDate($this->expected_checkin);
        }

        // TODO: set created_by to id of currently logged user
        return $message;
    }

}
