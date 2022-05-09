<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends APIController
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * @OA\Get(
     *     path="/api/v1/categories",
     *     description="Returns All Categories",
     *     tags={"categories"},
     *     @OA\Parameter(
     *          name="search",
     *          in="path",
     *          description="By passing this parameter you can filter the result",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="page",
     *          in="path",
     *          description="sets page number",
     *          required=true,
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="pagesize",
     *          in="path",
     *          description="sets page size",
     *          required=false,
     *          @OA\Schema(type="integer")
     *      ),
     *
     *     @OA\Response(
     *      response = 200,
     *      description = "Returns All Categories",
     *      @OA\JsonContent(
     *            @OA\Property(property="name", type="string", example="Category 1"),
     *            @OA\Property(property="slug", type="string", example="category-1")
     *        )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => 'string',
            'page' => 'required|numeric',
            'pageSize' => 'nullable|numeric'
        ]);

        $categories = $this->categoryRepository->paginate($request->page, $request->pagesize ?? 20,
            $request->search, ['slug', 'name']);


        return $this->respondSuccess('لیست دسته بندی ها', $categories['data']);

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|min:2|max:255',
            'slug' => 'required|string|min:2|max:255',
        ]);
        $createdCategory = $this->categoryRepository->store([
            'name' => $request->name,
            'slug' => $request->slug
        ]);
        return $this->respondCreated('دسته بندی با موفقیت ایجاد شد.', [
            'name' => $createdCategory->getName(),
            'slug' => $createdCategory->getSlug()
        ]);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', Rule::exists('categories', 'id')]
        ]);

        if (!$this->categoryRepository->delete($request->id))
            return $this->respondInternalError('خطا در حذف دسته بندی');

        return $this->respondSuccess('دسته بندی با موفققیت حذف شد.', []);
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', Rule::exists('categories', 'id')],
            'name' => 'required|string|min:2|max:255',
            'slug' => 'required|string|min:2|max:255',
        ]);

        try {
            $updatedUser = $this->categoryRepository->update($request->id, [
                'name' => $request->name,
                'slug' => $request->slug
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطا در بروزرسانی دسته بندی');
        }

        return $this->respondSuccess('دسته بندی با موفقیت بروزرسانی گردید.', [
            'name' => $updatedUser->getName(),
            'slug' => $updatedUser->getSlug()
        ]);
    }
}
