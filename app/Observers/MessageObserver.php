<?php

namespace App\Observers;

use App\Events\Chat\MessageWasSent;
use App\Models\Advertise;
use App\Models\Message;
use App\Models\MessageNotification;

/**
 * Class MessageObserver
 * @package App\Observers
 */
class MessageObserver {

    /**
     * Handle the Advertise "created" event.
     *
     * @param Message $message
     * @return void
     */
    public function created(Message $message) {
        MessageNotification::make($message, $message->conversation);
    }

    /**
     * Handle the Advertise "updated" event.
     *
     * @param Message $message
     * @return void
     */
    public function updated(Message $message) { }

    /**
     * Handle the Advertise "deleted" event.
     *
     * @param Message $message
     * @return void
     */
    public function deleted(Message $message) { }

    /**
     * Handle the Advertise "restored" event.
     *
     * @param Message $message
     * @return void
     */
    public function restored(Message $message) { }

    /**
     * Handle the Advertise "force deleted" event.
     *
     * @param Message $message
     * @return void
     */
    public function forceDeleted(Message $message) { }
}
