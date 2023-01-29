<?php

namespace App\Http\Controllers\Api\V1\Admin\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\Category\FilterRequest;
use App\Http\Resources\V1\Admin\Category\FilterResource;
use App\Models\Filter;
use App\Repositories\V1\Admin\Category\FilterRepository;
use App\Traits\ApiResponseAble;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

/**
 * Class FilterController
 * @package App\Http\Controllers\Api\V1\Admin\Category
 */
class FilterController extends Controller {

    use ApiResponseAble;

    /**
     * FilterController constructor.
     * @param FilterRepository $filterRepository
     */
    public function __construct(private FilterRepository $filterRepository) { }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function list(Request $request): AnonymousResourceCollection {
        return FilterResource::collection(
            $this->filterRepository->paginate($request->query('per_page'))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FilterRequest $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function store(FilterRequest $request): AnonymousResourceCollection|JsonResponse {
        try {
            $filters = $this->filterRepository->create($request->all());

            return FilterResource::collection($filters);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->clientErrorResponse($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param Filter $filter
     * @return FilterResource
     */
    public function show(Filter $filter): FilterResource {
        return new FilterResource(
            $filter->load($filter->getRelations())
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FilterRequest $request
     * @return AnonymousResourceCollection|JsonResponse
     */
    public function update(FilterRequest $request): AnonymousResourceCollection|JsonResponse {
        try {
            $filters = $this->filterRepository->updateFilter($request->all());

            return FilterResource::collection($filters);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->clientErrorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Filter $filter
     * @return JsonResponse
     */
    public function destroy(Filter $filter): JsonResponse {
        $filter->delete();
        return $this->success('', __('messages.ITEM_DELETED'));
    }
}
