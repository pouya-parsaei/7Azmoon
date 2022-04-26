<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends APIController
{
    public function __construct(private CategoryRepositoryInterface $categoryRepository)
    {
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
        return $this->respondCreated('دسته بندی با موفقیت ایجاد شد.',[
            'name' => $createdCategory->getName(),
            'slug' => $createdCategory->getSlug()
        ]);
    }
}
