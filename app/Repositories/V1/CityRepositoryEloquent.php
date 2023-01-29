<?php

namespace App\Repositories\V1;

use App\Repositories\V1\Base\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class CityRepositoryEloquent
 * @package App\Repositories\V1
 */
class CityRepositoryEloquent extends BaseRepository implements CityRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'name' => 'LIKE',
        'state_code',
        'state_id'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string {
        return \App\Models\City::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot() {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param string $latitude
     * @param string $longitude
     * @return object|null
     */
    public function findCityByLatLong(string $latitude, string $longitude): object|null {
        return \DB::table('cities')
            ->selectRaw("cities.id, cities.latitude, cities.longitude, (
                3959 * acos (
                  cos (radians($latitude))
                  * cos(radians(latitude))
                  * cos(radians(longitude) - radians($longitude))
                  + sin(radians($latitude))
                  * sin(radians(latitude))
                )
              ) AS distance"
            )
            ->having('distance', '<', 2)->first(); // 2 ml
    }
}
