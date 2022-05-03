<?php

namespace App\Repositories\Eloquent;

use App\Entities\Category\CategoryEloquentEntity;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepositoryInterface
{
    protected $model = Category::class;

    public function store(array $data)
    {
        $createdCategory = parent::store($data);
        return new CategoryEloquentEntity($createdCategory);
    }

    public function update(int $id, array $data)
    {
        return !parent::update($id, $data) ? throw new \Exception('خطا در بروزرسانی اطلاعات') :
            new CategoryEloquentEntity(parent::find($id));
    }
}
