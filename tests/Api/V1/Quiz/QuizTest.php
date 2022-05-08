<?php

namespace Tests\Api\V1\Quiz;

use App\Repositories\Contracts\QuizRepositoryInterface;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\Api\Contracts\CategoryMaker;
use Tests\Api\Contracts\Faker;
use Tests\Api\Contracts\QuestionMaker;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use DatabaseMigrations, Faker,QuestionMaker;

    public function test_ensure_can_create_quiz()
    {
        $this->setUpFaker();

        $category = $this->createCategories()[0];

        $examData = [
            'category_id' => (string)$category->getId(),
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'start_date' => '1401/01/31 03:10',
            'duration' => (string)random_int(5, 60),
        ];

        $response = $this->call('POST', 'api/v1/quizzes', $examData);

        $this->assertEquals(201, $response->getStatusCode());
        $convertedData = json_decode($response->getContent(), true)['data'];
        $this->assertEquals('2022-04-20 03:10:00',$convertedData['start_date']);
        $this->seeInDatabase('quizzes', $convertedData);
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_ensure_can_delete_a_quiz()
    {
        $quiz = $this->createQuiz()[0];

        $response = $this->call('DELETE', 'api/v1/quizzes', [
            'id' => $quiz->getId()
        ]);

        $this->assertResponseOk();
        $this->notSeeInDatabase('quizzes', [
            'category_id' => $quiz->getCategoryId(),
            'id' => $quiz->getId(),
            'title' => $quiz->getTitle(),
            'description' => $quiz->getDescription(),
            'start_date' => $quiz->getStartDate()
        ]);
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_ensure_can_get_quizzes()
    {
        $this->createQuiz(30);
        $pageSize = 5;
        $response = $this->call('GET', 'api/v1/quizzes', [
            'page' => 2,
            'pagesize' => $pageSize
        ]);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertResponseOk();
        $this->assertCount($pageSize, $data);
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

    }

    public function test_ensure_can_get_filtered_quizzes()
    {
        $quizzes = $this->createQuiz(40);
        $pageSize = 5;
        $searchKey = $quizzes[array_rand($quizzes)]->getTitle();

        $response = $this->call('GET', 'api/v1/quizzes', [
            'page' => 1,
            'pagesize' => $pageSize,
            'search' => $searchKey
        ]);

        $data = json_decode($response->getContent(), true)['data'];

        foreach ($data as $quiz) {
            $this->assertEquals($searchKey, $quiz['title']);
        }
        $this->assertResponseOk();
        $this->assertLessThanOrEqual($pageSize, count($data));
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

    }

    public function test_ensure_can_update_quiz()
    {
        $quiz = $this->createQuiz()[0];

        $quizOldData = [
            'category_id' => (string)$quiz->getCategoryId(),
            'id' => (string)$quiz->getId(),
            'title' => $quiz->getTitle(),
            'description' => $quiz->getDescription(),
            'start_date' => $quiz->getStartDate(),
            'is_active' => (string)$quiz->getIsActive(),
        ];
        $categories = $this->createCategories(10);
        $dataToBeUpdated = [
            'id' => (string)$quiz->getId(),
            'category_id' => (string)$categories[array_rand($categories)]->getId(),
            'title' => $this->faker->word,
            'description' => $this->faker->sentence,
            'start_date' => '1404/03/10 03:10',
            'duration' => (string)random_int(5, 60),
            'is_active' => (string)array_rand([true,false]),

        ];

        $response = $this->call('PUT','api/v1/quizzes',$dataToBeUpdated);

        $this->assertResponseOk();
        $convertedData = json_decode($response->getContent(), true)['data'];
        $this->assertEquals('2025-05-31 03:10:00',$convertedData['start_date']);
        $this->seeInDatabase('quizzes', $convertedData);
        $this->notSeeInDatabase('quizzes',$quizOldData);
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }


}
