<?php

namespace App\Http\Resources\V1\Admin\Category;

use App\Http\Resources\V1\MediaResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

/**
 * Class CategoryResource
 * @package App\Http\Resources\V1\Admin\Category
 *
 * @property mixed id
 * @property mixed slug
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed name
 * @property object category
 * @property object picture
 * @property mixed advertises_count
 */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable {
        if ($this->resource instanceof LengthAwarePaginator) {
            return parent::toArray($request);
        }

        return [
            'id' => $this->whenNotNull($this->id),
            'name' => $this->whenNotNull($this->name),
            'slug' => $this->whenNotNull($this->slug),
            'created_at' => $this->whenNotNull($this->created_at),
            'updated_at' => $this->whenNotNull($this->updated_at),
            'advertises_count' => $this->whenNotNull($this->advertises_count),
            'picture' => $this->whenLoaded('picture', function () {
                return MediaResource::make($this->picture);
            }),
            'category' => $this->whenLoaded('category', function () {
                return CategoryResource::make($this->category);
            }),
            'filters' => FilterResource::collection($this->whenLoaded('filters')),
            'parentCategories' => CategoryResource::collection($this->whenLoaded('parentCategories')),
            'subCategories' => CategoryResource::collection($this->whenLoaded('subCategories')),
            'allSubCategories' => CategoryResource::collection($this->whenLoaded('allSubCategories')),
        ];
    }
}
