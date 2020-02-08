<?php

namespace Modules\Klusbib\Notifications;

use App\Notifications\CheckinAccessoryNotification;
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
class NotifyAccessoryCheckin extends CheckinAccessoryNotification
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
    public function via()
    {
        \Log::debug('NotifyAccessoryCheckin::via');
        $notifyBy = parent::via();
        if (isset($this->target) && isset($this->target->employee_num)) {
            \Log::debug('use Klusbib');
            // Skip Klusbib notification if klusbib user id (=employee_num) is not set (not a Klusbib user)
            array_push($notifyBy, KlusbibChannel::class);
            Log::debug("notifications:" . \json_encode($notifyBy));
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
        Log::debug("Notify Accessory checkin to Klusbib lending API");

        $target = $this->target;
        $admin = $this->admin;
        $item = $this->item;
        $note = $this->note;
        $returnedDate = new \DateTime();
        Log::debug("target:" . $target->employee_num
            . ";item=" . $item->id
            . ";returnedDate=" . $returnedDate->format('Y-m-d'));

        $message = (new LendingApiMessage(LendingApiMessage::METHOD_UPDATE, $target, $item, $note, $admin))
            ->userId($target->employee_num)->tool($item->id, LendingApiMessage::TOOL_TYPE_ACCESSORY)
            ->startDate($item->last_checkout)
            ->returnedDate($returnedDate->format('Y-m-d'));

        return $message;
    }

}
