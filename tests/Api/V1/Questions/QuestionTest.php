<?php

namespace Tests\Api\V1\Questions;

use App\Consts\QuestionStatus;
use App\Utilities\JsonUtility;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\Api\Contracts\Faker;
use Tests\Api\Contracts\QuizMaker;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use DatabaseMigrations, Faker, QuizMaker;

    public function test_ensure_can_create_questtion()
    {
        $quizzes = $this->createQuiz(10);

        $questionData = [
            'quiz_id' => (string)$quizzes[array_rand($quizzes)]->getId(),
            'title' => $this->faker->sentence,
            'options' => [
                1 => ['text' => 'Answer number one', 'is_correct' => 0],
                2 => ['text' => 'Answer number two', 'is_correct' => 1],
                3 => ['text' => 'Answer number three', 'is_correct' => 0],
                4 => ['text' => 'Answer number four', 'is_correct' => 0],
            ],
            'score' => random_int(1, 5),
            'activation_status' => QuestionStatus::ACTIVE,
        ];

        $response = $this->call('POST', 'api/v1/questions', $questionData);

        $returnedData = json_decode($response->getContent(), true)['data'];

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($questionData['quiz_id'], $returnedData['quiz_id']);
        $this->assertEquals($questionData['title'], $returnedData['title']);
        $this->assertEquals($questionData['options'], $returnedData['options']);
        $this->assertEquals($questionData['score'], $returnedData['score']);
        $this->assertEquals($questionData['activation_status'], $returnedData['activation_status']);
        $this->seeInDatabase('questions', [
            'id' => $returnedData['id'],
            'quiz_id' => $returnedData['quiz_id'],
            'title' => $returnedData['title'],
            'options' => JsonUtility::castToJson($returnedData['options']),
            'activation_status' => $returnedData['activation_status'],
        ]);
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }
}
