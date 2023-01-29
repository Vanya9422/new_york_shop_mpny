<?php

namespace App\Repositories\V1;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CityRepository
 * @package App\Repositories\V1
 */
interface CityRepository extends RepositoryInterface
{

    /**
     * @param string $latitude
     * @param string $longitude
     * @return object|null
     */
    public function findCityByLatLong(string $latitude, string $longitude): object|null;
}
