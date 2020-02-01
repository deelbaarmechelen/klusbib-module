<?php
namespace Modules\Klusbib\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Endpoints\Lendings;
use Modules\Klusbib\Models\Api\Lending;

class KlusbibChannel
{

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toKlusbib($notifiable);
        Log::debug('Klusbib Channel: message to send=' . \json_encode($message));
        // Send notification to the $notifiable instance...
        $params = array(
            'user_id' => $message->getUserId(),
            'tool_id' => $message->getToolId(),
            'start_date' => $message->getStartDate(),
            'due_date' => $message->getDueDate(),
            'comments' => $message->getComments()
        );
        Lending::create($params);
    }

}