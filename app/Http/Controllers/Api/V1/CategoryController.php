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
