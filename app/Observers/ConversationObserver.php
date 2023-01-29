<?php

namespace App\Observers;

use App\Enums\Users\TicketStatuses;
use App\Events\Chat\ConversationUpdated;
use App\Models\Conversation;

/**
 * Class ConversationObserver
 * @package App\Observers
 */
class ConversationObserver {

    /**
     * Handle the Conversation "created" event.
     *
     * @param Conversation $conversation
     * @return void
     */
    public function created(Conversation $conversation) { }

    /**
     * Handle the Advertise "updated" event.
     *
     * @param Conversation $conversation
     * @return void
     */
    public function updated(Conversation $conversation) {
        if ($this->canDelete($conversation)) {
            $conversation->delete();
        } else {
            if($this->enabledBroadcasts()) {

                if (!$conversation->started) {
                    $conversation::unsetEventDispatcher();
                    $conversation->makeStarted();
                }

                /**
                 * Если изменился модератор то отправляем новому Модератору
                 */
                if ($conversation->getOriginal('starter_id') !== $conversation->starter_id) {
                    $participant_id = $conversation->starter_id;
                }

                if($conversation->ticket_id) {
                    $conversation->load('ticket');

                    if ($conversation->ticket->status === TicketStatuses::VIEWED) {
                        $conversation->ticket->update(['status' => TicketStatuses::EXPECTATION]);
                    }
                }

                broadcast(ConversationUpdated::make($conversation, $participant_id ?? null))->toOthers();
            }
        }
    }

    /**
     * Handle the Advertise "deleted" event.
     *
     * @param Conversation $conversation
     * @return void
     */
    public function deleted(Conversation $conversation) { }

    /**
     * Handle the Advertise "restored" event.
     *
     * @param Conversation $conversation
     * @return void
     */
    public function restored(Conversation $conversation) { }

    /**
     * Handle the Advertise "force deleted" event.
     *
     * @param Conversation $conversation
     * @return void
     */
    public function forceDeleted(Conversation $conversation) { }

    /**
     * @param $conversation
     * @return bool
     */
    public function canDelete($conversation): bool {
        return $conversation->isDeleteFromStarter() && $conversation->isDeleteFromReceiver();
    }

    /**
     * @return bool
     */
    public function enabledBroadcasts(): bool {
        return \Chat::getInstance()->broadcasts();
    }
}
