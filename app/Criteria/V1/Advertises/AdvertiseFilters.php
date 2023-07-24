<?php

namespace App\Criteria\V1\Advertises;

use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class SearchCriteria
 * @package App\Criteria\V1
 */
class AdvertiseFilters implements CriteriaInterface
{
    protected Request $request;

    /**
     * SearchCriteria constructor.
     */
    public function __construct()
    {
        $this->request = app(Request::class);
    }

    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $filters = json_decode($this->request->query('filters'), true);

        return $model->where(function ($query) use ($filters) {
            $this->getQueryFiltersWithRelations($query, $filters);
        });
    }

    /**
     * @param $query
     * @param $filters
     * @return void
     */
    private function getQueryFilters(&$query, $filters) {
        $whereOrWhere = count($filters) === 1 ? 'where' : 'orWhere';

        foreach ($filters as $key => $filter) {
            $query->{$whereOrWhere} (function ($q) use ($key, $filter) {

                if (!is_array($filter) && $key === $filter) {
                    $q->where('answer_ids', 'LIKE', "%[$key]%");
                }

                if (!is_array($filter) && $key !== $filter) {
                    $q->where('answer_ids', 'LIKE', "%[$key][$filter]%");
                }

                if (is_array($filter)) {
                    $this->getQueryFilters($q, $filter);
                }
            });
        }
    }

    /**
     * @param $query
     * @param $filters
     * @return void
     */
    private function getQueryFiltersWithRelations(&$query, $filters) {
        $whereOrWhere = count($filters) === 1 ? 'where' : 'orWhere';

        foreach ($filters as $key => $filter) {
            $query->{$whereOrWhere} (function ($q) use ($key, $filter) {

                if (!is_array($filter) && $key === $filter) {
                    $q->whereHas('answers', function($q) use ($key) {
                        $q->whereId($key);
                    });
                }

                if (!is_array($filter) && $key !== $filter) {
                    $q->whereHas('answers', function($q) use ($key) {
                        $q->whereId($key);
                    });
                    $q->whereHas('answers', function($q) use ($filter) {
                        $q->whereId($filter);
                    });
                }

                if (is_array($filter)) {
                    $this->getQueryFilters($q, $filter);
                }
            });
        }
    }
}
