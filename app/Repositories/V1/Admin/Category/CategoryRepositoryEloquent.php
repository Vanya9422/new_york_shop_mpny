<?php

namespace App\Repositories\V1\Admin\Category;

use App\Models\Category;
use App\Repositories\V1\Base\BaseRepository;
use App\Traits\UploadAble;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories\Api\V1\Admin;
 */
class CategoryRepositoryEloquent extends BaseRepository implements CategoryRepository {

    use UploadAble;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * CategoryRepositoryEloquent constructor.
     * @param Application $app
     */
    public function __construct(Application $app) {
        parent::__construct($app);

        $localeName = "name->" . app()->getLocale();

        $this->fieldSearchable = [
            'parent_id',
            $localeName
        ];
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model(): string {
        return \App\Models\Category::class;
    }

    /**
     * Boot up the repository, pushing criteria
     * @throws RepositoryException
     */
    public function boot() {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param array $attributes
     * @throws ValidatorException|\Throwable
     * @return mixed
     */
    public function create(array $attributes): mixed {
        DB::transaction(function () use ($attributes, &$category) {
            $category = parent::create($attributes);
            $this->addImageIfExistsFile($category, $attributes);
        });

        return $category->fresh();
    }

    /**
     * @param array $attributes
     * @param Category $category
     * @throws ValidatorException|\Throwable
     * @return mixed
     */
    public function updateCategory(array $attributes, Category $category): Category {

        DB::transaction(function () use ($attributes, &$category) {
            $category = parent::update($attributes, $category->id);
            $this->addImageIfExistsFile($category, $attributes, true);
        });

        return $category;
    }

    /**
     * @param Category $category
     * @return Category
     */
    public function duplicateCategory(Category $category): Category {
        $category = $category->replicate(['id', 'slug', 'created_at', 'updated_at']);
        $category->save();
        return $category;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getTopCategories(string $name): mixed {
        $likeAdvertiseByName = function ($query) use ($name) {
            $query->where('name', 'LIKE', "%$name%");
        };

        return $this->getModel()::whereHas('advertises', $likeAdvertiseByName)
            ->withCount(['advertises as advertise_count' => $likeAdvertiseByName])
            ->orderBy('advertise_count', 'desc')
            ->with('picture')
            ->take(4)
            ->get();
    }

    /**
     * @param Category $category
     * @param array $attributes
     * @param bool $withDelete
     * @throws \Illuminate\Validation\ValidationException
     */
    private function addImageIfExistsFile(Category $category, array $attributes, bool $withDelete = false): void {
        config(['audit.events' => ['created', 'updated']]);

        if (existsUploadAbleFileInArray($attributes)) {

            if ($withDelete) {
                /**
                 * Удаляем старую картинку
                 */
                $category->clearMediaCollection(\App\Enums\MediaCollections::PICTURE_COLLECTION);
            }

            /**
             * Добовляем Новую
             */
            $this->addResponsiveImage(
                $category,
                $attributes['picture'],
                \App\Enums\MediaCollections::PICTURE_COLLECTION
            );
        }
    }

    /**
     * @param string $search
     * @return array
     */
    public function searchCategoriesByAdvertiseCounts(string $search): array {
        $function = function($q) use ($search) {
            $q->where('name', '=', $search);
        };

        $categories = $this->getModel()->newQuery()
            ->select(['name', 'id', 'slug', 'parent_id'])
            ->whereHas('advertises', $function)
            ->withCount(['advertises' => $function])
            ->with(['parentCategories:id,slug,name,parent_id'])
            ->orderBy('advertises_count', 'DESC')
            ->take(3)
            ->get();

        $advertises = \DB::table('advertises')
            ->select(['advertises.name', DB::raw('count(*) as total')])
            ->where('name', 'LIKE', "%$search%")
            ->groupBy('advertises.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->toArray();

        return [$categories, $advertises];
    }

    /**
     * @param array $categories_ids
     * @return void
     */
    public function multipleDelete(array $categories_ids): void {
        $this->deleteWhere([['id', 'IN', $categories_ids]]);
    }
}
