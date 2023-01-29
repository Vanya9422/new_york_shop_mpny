<?php

namespace App\Http\Resources\V1\User;

use App\Http\Resources\V1\AdvertiseResource;
use App\Http\Resources\V1\MediaResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 * @package App\Http\Resources\V1\User
 *
 * @property mixed id
 * @property mixed first_name
 * @property mixed last_name
 * @property mixed full_name
 * @property mixed email
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed roles
 * @property mixed phone
 * @property object avatar
 * @property mixed registered
 * @property mixed verified_at
 * @property mixed advertise_favorites_ids
 * @property array permissions_ids
 * @property int unread_notifications_count
 * @property string phone_view
 * @property mixed banned
 * @property mixed advertises_count
 * @property mixed canceled_advertises_count
 */
class UserResource extends JsonResource {

    /**
     * @var bool
     */
    public static $wrap = false;

    /**
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array|\JsonSerializable|\Illuminate\Contracts\Support\Arrayable {

        if ($this->resource instanceof LengthAwarePaginator) {
            return parent::toArray($request);
        }

        return [
            'id' => $this->whenNotNull($this->id),
            'first_name' => $this->whenNotNull($this->first_name),
            'last_name' => $this->whenNotNull($this->last_name),
            'full_name' => $this->whenNotNull($this->full_name),
            'phone' => $this->whenNotNull($this->phone),
            'email' => $this->whenNotNull($this->email),
            'registered' => $this->whenNotNull($this->registered),
            'phone_view' => $this->whenNotNull($this->phone_view),
            'verified_at' => $this->whenNotNull($this->verified_at),
            'banned' => $this->whenNotNull($this->banned),
            'permissions_ids' => $this->whenNotNull($this->permissions_ids),
            'created_at' => $this->whenNotNull($this->created_at),
            'updated_at' => $this->whenNotNull($this->updated_at),
            'favorites_ids' => $this->whenNotNull($this->advertise_favorites_ids),
            'advertises_count' => $this->whenNotNull($this->advertises_count),
            'canceled_advertises_count' => $this->whenNotNull($this->canceled_advertises_count),
            'advertises' => AdvertiseResource::collection($this->whenLoaded('advertises')),
            'block_list' => UserResource::collection($this->whenLoaded('block_list')),
            'unread_notifications_count' => $this->whenNotNull($this->unread_notifications_count),
//            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'role' => $this->whenLoaded('roles', function () {
                return count($this->roles) ? RoleResource::make($this->roles[0]) : [];
            }),
            'avatar' => $this->whenLoaded('avatar', function () {
                return MediaResource::make($this->avatar);
            }),
        ];
    }
}
