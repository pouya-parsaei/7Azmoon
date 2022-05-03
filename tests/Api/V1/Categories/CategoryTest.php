<?php

namespace Tests\Api\V1\Categories;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Tests\Api\Contracts\Faker;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    use Faker;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function test_ensure_can_create_new_category()
    {
        $newCategory = ['name' => 'category 1', 'slug' => 'category-1'];
        $response = $this->call('POST', 'api/v1/categories', $newCategory);
        $this->assertEquals(201, $response->getStatusCode());
        $this->seeInDatabase('categories', $newCategory);
        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'name',
                'slug'
            ]
        ]);

    }

    public function test_ensure_can_update_category()
    {
        $this->setUpFaker();
        $category = $this->createCategories()[0];
        $categoryData = [
            'id' => (string)$category->getId(),
            'name' => $this->faker->name,
            'slug' => $this->faker->slug
        ];
        $response = $this->call('PUT', 'api/v1/categories', $categoryData);

        $this->assertEquals(200, $response->getStatusCode());
        $this->seeInDatabase('categories', $categoryData);
        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'name',
                'slug'
            ]
        ]);

    }

    public function test_ensure_can_delete_a_category()
    {
        $category = $this->createCategories()[0];

        $response = $this->call('DELETE', 'api/v1/categories', [
            'id' => (string)$category->getId()
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

        $this->notSeeInDatabase('categories', [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug()
        ]);
    }

    public function test_ensure_can_get_filtered_categories()
    {
        $categories = $this->createCategories(30);
        $pageSize = 5;
        $categorySlug = $categories[array_rand($categories)]->getSlug();
        $response = $this->call('GET', 'api/v1/categories', [
            'search' => $categorySlug,
            'page' => 1,
            'pagesize' => $pageSize
        ]);

        $data = json_decode($response->getContent(), true)['data'];

        foreach ($data as $category) {
            $this->assertEquals($categorySlug, $category['slug']);
        }

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }


    public function test_ensure_can_get_categories()
    {
        $this->createCategories(30);
        $pageSize = 5;

        $response = $this->call('GET', 'api/v1/categories', [
            'page' => 1,
            'pagesize' => $pageSize
        ]);
        $data = json_decode($response->getContent(), true)['data'];

        $this->assertCount($pageSize, $data);
        $this->assertResponseOk();
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }


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
