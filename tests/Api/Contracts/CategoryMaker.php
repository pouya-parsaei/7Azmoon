<?php

namespace Tests\Api\Contracts;

use App\Repositories\Contracts\CategoryRepositoryInterface;

trait CategoryMaker
{
    private function createCategories($count = 1)
    {
        $this->setUpFaker();

        $categoryRepository = $this->app->make(CategoryRepositoryInterface::class);

        $categories = [];

        foreach (range(0, $count) as $item) {
            $newCategory = [
                'name' => $this->faker->name,
                'slug' => $this->faker->slug
            ];
            $categories[] = $categoryRepository->store($newCategory);
        }

        return $categories;
    }
}
