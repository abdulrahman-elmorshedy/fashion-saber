<?php
namespace InnoShop\RestAPI\PanelApiControllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InnoShop\Common\Models\Category;
use InnoShop\Common\Repositories\CategoryRepo;
use InnoShop\Common\Resources\CategoryName;
use InnoShop\Common\Resources\CategorySimple;
use InnoShop\Panel\Requests\CategoryRequest;
use InnoShop\RestAPI\FrontApiControllers\BaseController;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\UrlParam;

#[Group('Panel - Categories')]
class CategoryController extends BaseController
{
    /**
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('List categories')]
    #[QueryParam('per_page', 'integer', required: false, example: 15)]
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->all();
        
        // If it's a picker and parent_id is 0 or not set, we might want ALL categories (flat)
        // or let the builder handle it. Some pickers send parent_id=0 but want to see everything
        // if they are not tree-aware.
        if ($request->has('picker') && isset($filters['parent_id']) && $filters['parent_id'] == 0) {
            unset($filters['parent_id']);
        }

        $perPage = $request->get('per_page', 100); // Larger per page for pickers

        $builder = CategoryRepo::getInstance()->builder($filters);
        
        if ($request->has('flat') || $request->has('picker')) {
            $builder->withOnly(['translation']); 
        }

        $categories = $builder->paginate($perPage);

        return CategorySimple::collection($categories);
    }

    /**
     * Get single category by ID.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    #[Endpoint('Get category detail')]
    #[UrlParam('id', 'integer', description: 'Category ID', example: 1)]
    public function show(int $id): JsonResponse
    {
        try {
            $category = CategoryRepo::getInstance()->builder()->findOrFail($id);

            return json_success('Success', new CategorySimple($category));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Get categories by IDs.
     * GET /api/panel/categories/names?ids=1,2,3
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     * @throws Exception
     */
    #[Endpoint('Get categories by IDs')]
    #[QueryParam('ids', 'string', description: 'Comma-separated category IDs', required: true, example: '1,2,3')]
    public function names(Request $request): AnonymousResourceCollection
    {
        $categories = CategoryRepo::getInstance()->getListByCategoryIDs($request->get('ids'));

        return CategoryName::collection($categories);
    }

    /**
     * Autocomplete by keyword.
     * GET /api/panel/categories/autocomplete?keyword=xxx
     *
     * @param  Request  $request
     * @return AnonymousResourceCollection
     */
    #[Endpoint('Autocomplete categories')]
    #[QueryParam('keyword', 'string', required: false)]
    public function autocomplete(Request $request): AnonymousResourceCollection
    {
        $categories = CategoryRepo::getInstance()->autocomplete($request->get('keyword') ?? '');

        return CategoryName::collection($categories);
    }

    /**
     * Update a category.
     * PUT /api/panel/categories/{id}
     *
     * @param  Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    #[Endpoint('Update category')]
    #[UrlParam('id', 'integer', description: 'Category ID', example: 1)]
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $data     = $request->all();
            CategoryRepo::getInstance()->update($category, $data);

            return json_success('Category updated successfully', new CategorySimple($category));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Partial update a category.
     * PATCH /api/panel/categories/{id}
     *
     * @param  CategoryRequest  $request
     * @param  int  $id
     * @return JsonResponse
     */
    #[Endpoint('Partial update category')]
    #[UrlParam('id', 'integer', description: 'Category ID', example: 1)]
    public function patch(CategoryRequest $request, int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $data     = $request->validated();
            CategoryRepo::getInstance()->patch($category, $data);

            return update_json_success(new CategorySimple($category));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Create a new category.
     * POST /api/panel/categories
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    #[Endpoint('Create category')]
    public function store(Request $request): JsonResponse
    {
        try {
            $data     = $request->all();
            $category = CategoryRepo::getInstance()->create($data);

            return json_success('Category created successfully', new CategorySimple($category));
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }

    /**
     * Delete a category.
     * DELETE /api/panel/categories/{id}
     *
     * @param  int  $id
     * @return JsonResponse
     */
    #[Endpoint('Delete category')]
    #[UrlParam('id', 'integer', description: 'Category ID', example: 1)]
    public function destroy(int $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->children()->count()) {
                return json_fail('Cannot delete category with children');
            }

            CategoryRepo::getInstance()->destroy($category);

            return json_success('Category deleted successfully');
        } catch (Exception $e) {
            return json_fail($e->getMessage());
        }
    }
}
