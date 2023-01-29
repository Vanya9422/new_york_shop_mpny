<?php

namespace App\Http\Middleware\Api\V1\Chat;

use App\Exceptions\Chat\ConversationIncorrectParticipantException;
use App\Models\Conversation;
use App\Traits\ApiResponseAble;
use Closure;
use Illuminate\Http\Request;

/**
 * Class CheckConversation
 * @package App\Http\Middleware\Chat
 */
class CheckConversation
{
    use ApiResponseAble;

    /**
     * @param Request $request
     * @param Closure $next
     * @throws \App\Exceptions\Chat\ConversationIncorrectParticipantException
     * @throws \Throwable
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Conversation $conversation */
        $conversation = $request->route('conversation');
        throw_if(!$conversation->status, new ConversationIncorrectParticipantException('Conversation Is Closed'));

        \Chat::getInstance()
            ->setParticipant($request->user())
            ->conversation($conversation)
            ->throwIsIncorrectParticipant();

        return $next($request);
    }
}
