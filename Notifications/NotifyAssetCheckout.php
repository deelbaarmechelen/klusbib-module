<?php

namespace Modules\Klusbib\Notifications;

use App\Notifications\CheckoutAssetNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Channels\KlusbibChannel;
use Modules\Klusbib\Notifications\Messages\LendingApiMessage;

/**
 * Extension of CheckoutAssetNotification
 * Adds Klusbib specific extra notifications on top of the Snipe IT notifications
 *
 * @package Modules\Klusbib\Notifications
 */
class NotifyAssetCheckout extends CheckoutAssetNotification
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
        $notifyBy = parent::via();
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
        Log::debug("Notify asset checkout to Klusbib lending API");
//        'item'          => $this->item,
//                'admin'         => $this->admin,
//                'note'          => $this->note,
//                'log_id'        => $this->log_id,
//                'target'        => $this->target,
//                'fields'        => $fields,
//                'eula'          => $eula,
//                'req_accept'    => $req_accept,
//                'accept_url'    =>  url('/').'/account/accept-asset/'.$this->log_id,
//                'last_checkout' => $this->last_checkout,
//                'expected_checkin'  => $this->expected_checkin,
        $target = $this->target;
        $admin = $this->admin;
        $item = $this->item;
        $note = $this->note;

        $message = (new LendingApiMessage(LendingApiMessage::METHOD_CREATE, $target, $item, $note, $admin))
            ->userId($target->employee_num)->tool($item->id, LendingApiMessage::TOOL_TYPE_ASSET)
            ->startDate($this->last_checkout);

        if (($this->expected_checkin) && ($this->expected_checkin!='')) {
            $message = $message->dueDate($this->expected_checkin);
        }

        // TODO: set created_by to id of currently logged user
        return $message;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
//    public function toMail($notifiable)
    public function toMail()
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', 'https://laravel.com')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
