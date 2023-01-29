<?php

namespace App\Http\Controllers\Api\V1\Admin\Support;

use App\Enums\Users\TicketStatuses;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Admin\Support\ThemeResource;
use App\Http\Resources\V1\Admin\Support\TicketResource;
use App\Models\SupportTheme;
use App\Models\Ticket;
use App\Services\V1\Admin\SupportService;
use App\Traits\ApiResponseAble;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class SupportController
 * @package App\Http\Controllers\Api\V1\Users
 */
class SupportController extends Controller {

    use ApiResponseAble;

    /**
     * SupportController constructor.
     * @param SupportService $supportService
     */
    public function __construct(private SupportService $supportService) { }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function listThemes(Request $request): AnonymousResourceCollection {
        $close = TicketStatuses::CLOSE;
        $new = TicketStatuses::NEW;
        $viewed = TicketStatuses::VIEWED;
        $expectation = TicketStatuses::EXPECTATION;

        $theme = $request->query('theme', false);
        $ticketsByThemesCollection = SupportTheme::withCount([
            'tickets' => function ($q) use ($theme) {
                $q->when($theme, function ($q) use ($theme) {
                    $q->where('support_theme_id', '=', $theme);
                });
            }
        ])->get();

        return ThemeResource::collection($ticketsByThemesCollection)->additional([
            'counts' => \DB::table('tickets as t')
                ->selectRaw("count(CASE WHEN t.status = $new THEN 1 END) as new_count")
                ->selectRaw("count(CASE WHEN t.status = $viewed THEN 1 END) as viewed_count")
                ->selectRaw("count(CASE WHEN t.status = $expectation THEN 1 END) as expectation_count")
                ->selectRaw("count(CASE WHEN t.status = $close THEN 1 END) as close_count")
                ->first()
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function listTickets(Request $request): AnonymousResourceCollection {
        return TicketResource::collection(
            $this->supportService->ticket()->paginate($request->query('perPage'))
        );
    }

    /**
     * Действия для модераторов
     *
     * @param Ticket $ticket
     * @throws \Throwable
     * @return JsonResponse|TicketResource
     */
    public function acceptTicket(Ticket $ticket): JsonResponse|TicketResource {
        try {
            $message = __('messages.TICKET_ALREADY_ACCEPTED');

            if (!$ticket->moderator()->exists()) {
                $message = __('messages.TICKET_ACCEPTED');
                $ticket = $this->supportService->ticket()->acceptTicket(user(), $ticket);
            }

            return $this->success(['ticket' => new TicketResource($ticket)], $message, 'accepted');
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->clientErrorResponse($e->getMessage());
        }
    }

    /**
     * Действия для модераторов
     *
     * @param Ticket $ticket
     * @param string $action (expect or close)
     * @return JsonResponse|TicketResource
     */
    public function actionTicket(Ticket $ticket, string $action): JsonResponse|TicketResource {
        try {
            if (!$ticket->moderator_id) {
                $this->error(__('messages.UNPROCESSABLE'),'forbidden');
            }

            if($action === 'close') {
                $message = __('messages.TICKET_CLOSED');
            } else {
                $message = __('messages.SUCCESS_OPERATED');
            }

            $this->supportService->ticket()->{$action . 'Ticket'}($ticket);

            return $this->success('ok', $message);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->clientErrorResponse($e->getMessage());
        }
    }
}
