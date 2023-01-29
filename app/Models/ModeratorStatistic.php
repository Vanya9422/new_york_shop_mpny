<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ModeratorStatistic
 *
 * @package App\Models
 * @property int $id
 * @property int $verified_ads Общее колличествео проверенных объявлений
 * @property int $approved_ads Общее кол-во одобренных объявлений
 * @property int $viewed_ads Кол-во просмотренных объявлений (view details count)
 * @property int $rejected_ads Общее кол-во отказанных оъявлений.
 * @property int $unverified_ads Кол-во задержанных дней не проверенных публикаций
 * @property int $banned_users Кол-во забанненых пользователей
 * @property int $unbanned_users Кол-во разбаненных пользователей
 * @property int $closed_tickets Кол-во завершенных запросов в поддержке
 * @property int $pending_tickets Кол-во не завершенных запросов в поддержке.
 * @property int $request_to_another_manager Кол-во передевенных запросов поддержки на дургого менеджера
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Advertise $advertise
 * @property-read \App\Models\User|null $banned
 * @property-read \App\Models\User|null $moderator
 * @property-read \App\Models\Ticket $ticket
 * @property-read \App\Models\User|null $unBanned
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic newQuery()
 * @method static \Illuminate\Database\Query\Builder|ModeratorStatistic onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic query()
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereApprovedAds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereBannedUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereClosedTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic wherePendingTickets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereRejectedAds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereRequestToAnotherManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereUnbannedUsers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereUnverifiedAds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereVerifiedAds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ModeratorStatistic whereViewedAds($value)
 * @method static \Illuminate\Database\Query\Builder|ModeratorStatistic withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ModeratorStatistic withoutTrashed()
 * @mixin \Eloquent
 */
class ModeratorStatistic extends Model {

    use HasFactory, SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'type',
        'moderator_id',
        'advertise_id',
        'ticket_id',
        'banned_id',
        'unbanned_id',
    ];

    /**
     * @param array $attributes
     * @return static
     */
    public static function make(array $attributes): static {
//        \DB::table('moderator_statistics')
//            ->where('type', 1)
//            ->where('moderator_id', 4)
//            ->whereDate('created_at', '<=', \Carbon\Carbon::now()->subHours(24))->get();

        return static::create([
            'type' => $attributes['type'],
            'moderator_id' => $attributes['moderator_id'],
            'advertise_id' => $attributes['advertise_id'] ?? null,
            'ticket_id' => $attributes['ticket_id'] ?? null,
            'banned_id' => $attributes['banned_id'] ?? null,
            'unbanned_id' => $attributes['unbanned_id'] ?? null,
        ]);
    }

    /**
     * @return BelongsTo
     */
    public function banned(): BelongsTo {
        return $this->belongsTo(User::class, 'banned_id');
    }

    /**
     * @return BelongsTo
     */
    public function unBanned(): BelongsTo {
        return $this->belongsTo(User::class, 'unbanned_id');
    }

    /**
     * @return BelongsTo
     */
    public function moderator(): BelongsTo {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * @return BelongsTo
     */
    public function ticket(): BelongsTo {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * @return BelongsTo
     */
    public function advertise(): BelongsTo {
        return $this->belongsTo(Advertise::class);
    }
}
