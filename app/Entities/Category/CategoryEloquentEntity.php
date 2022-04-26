<?php

namespace App\Entities\Category;

use App\Models\Category;

class CategoryEloquentEntity implements CategoryEntity
{

    public function __construct(private Category $category)
    {

    }

    public function getId(): int
    {
        return $this->category->id;
    }

    public function getName(): string
    {
        return $this->category->name;

    }

    public function getSlug(): string
    {
        return $this->category->slug;
    }
}
