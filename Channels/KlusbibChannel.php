<?php
namespace Modules\Klusbib\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Endpoints\Lendings;
use Modules\Klusbib\Models\Api\Lending;
use Modules\Klusbib\Notifications\Messages\LendingApiMessage;

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

        if ($message->getMethod() == LendingApiMessage::METHOD_CREATE) {
            $params = array(
                'user_id' => $message->getUserId(),
                'tool_id' => $message->getToolId(),
                'tool_type' => $message->getToolType(),
                'start_date' => $message->getStartDate(),
                'due_date' => $message->getDueDate(),
                'comments' => $message->getComments()
            );
            Log::debug('Klusbib Channel: create lending=' . \json_encode($params));
            try {
                Lending::create($params);
            } catch (\Exception $ex) {
                Log::error("Unexpected error creating lending: " . $ex->getMessage());
            }
        }
        if ($message->getMethod() == LendingApiMessage::METHOD_UPDATE) {
            $lending = Lending::findActiveByUserTool( $message->getUserId(), $message->getToolId(), $message->getToolType());
            Log::debug('Klusbib Channel: lending found=' . \json_encode($lending));
            if (isset($lending)) {
                $params = array(
                    'returned_date' => $message->getReturnedDate(),
                    'comments' => empty($lending->comments) ? $message->getComments() : $lending->comments . " / On return: " . $message->getComments()
                );
                Log::debug('Klusbib Channel: update lending=' . \json_encode($params));
                try {
                    $lending->update($params);
                } catch (\Exception $ex) {
                    Log::error("Unexpected error creating lending: " . $ex->getMessage());
                }
             }
        }

    }

}