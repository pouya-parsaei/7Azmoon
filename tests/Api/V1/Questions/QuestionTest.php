<?php

namespace Tests\Api\V1\Questions;

use App\Consts\QuestionStatus;
use App\Utilities\JsonUtility;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\Api\Contracts\Faker;
use Tests\Api\Contracts\QuestionMaker;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use DatabaseMigrations, Faker, QuestionMaker;

    public function test_ensure_can_create_question()
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

    public function test_ensure_can_delete_question()
    {
        $question = $this->createQuestion()[0];
        $response = $this->call('DELETE','api/v1/questions',['id' => $question->getId()]);

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

        $this->notSeeInDatabase('questions',[
            'id' => $question->getId(),
            'quiz_id' => $question->getQuizId(),
            'title' => $question->getTitle(),
            'options' => JsonUtility::castToJson($question->getOptions()),
            'activation_status' => $question->getActivationStatus(),
        ]);


    }

    public function test_ensure_can_get_questions()
    {
        $questions = $this->createQuestion(30);
        $pageSize = random_int(1,10);
        $response = $this->call('GET','api/v1/questions',[
            'page' => random_int(1,3),
            'pagesize' => $pageSize
        ]);

        $data = json_decode($response->getContent(), true)['data']['data'];
        $this->assertResponseOk();
        $this->assertEquals($pageSize,count($data));
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

    }

    public function test_ensure_can_get_filtered_questions()
    {
        $questions = $this->createQuestion(30);
        $pageSize = random_int(1,10);
        $searchKey = $questions[array_rand($questions)]->getTitle();
        $response = $this->call('GET','api/v1/questions',[
            'page' => 1,
            'pagesize' => $pageSize,
            'search' => $searchKey
        ]);

        $data = json_decode($response->getContent(), true)['data']['data'];

        foreach ($data as $question) {
            $this->assertEquals($searchKey, $question['title']);
        }

        $this->assertResponseOk();
        $this->assertLessThanOrEqual($pageSize, count($data));

        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

    }

    public function test_ensure_can_get_specific_quiz_questions()
    {
        $questions = $this->createQuestion(40);
        $pageSize = random_int(5,10);
        $quizId = (string)$questions[array_rand($questions)]->getQuizId();
        $response = $this->call('get','api/v1/questions/get-quiz-questions',[
            'page' => 1,
            'pagesize' => $pageSize,
            'quiz_id' => $quizId
        ]);

        $data = json_decode($response->getContent(), true)['data']['data'];

        foreach ($data as $question) {
            $this->assertEquals($quizId, $question['quiz_id']);
        }
        $this->assertResponseOk();
        $this->assertLessThanOrEqual($pageSize, count($data));

        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

    }

    public function test_ensure_can_update_question()
    {
        $questions = $this->createQuestion(10);
        $quizId = (string)$questions[array_rand($questions)]->getQuizId();
        $questionId = (string)$questions[array_rand($questions)]->getId();
        $options = [
            1 => ['text' => $this->faker->sentence, 'is_correct' => 0],
            2 => ['text' => $this->faker->sentence, 'is_correct' => 1],
            3 => ['text' => $this->faker->sentence, 'is_correct' => 0],
            4 => ['text' => $this->faker->sentence, 'is_correct' => 0]
        ];
        shuffle($options);
        $dataToUpdate = [
            'id' => $questionId,
            'quiz_id' => $quizId,
            'title' => $this->faker->sentence(),
            'options' => $options,
            'score' => random_int(1, 10),
            'activation_status' => QuestionStatus::DE_ACTIVE
        ];
        $response = $this->call('PUT','api/v1/questions',$dataToUpdate);

        $returnedData = json_decode($response->getContent(), true)['data'];

        $this->assertResponseOk();
        $this->assertEquals($dataToUpdate['id'], $returnedData['id']);
        $this->assertEquals($dataToUpdate['quiz_id'], $returnedData['quiz_id']);
        $this->assertEquals($dataToUpdate['title'], $returnedData['title']);
        $this->assertEquals($dataToUpdate['options'], $returnedData['options']);
        $this->assertEquals($dataToUpdate['score'], $returnedData['score']);
        $this->assertEquals($dataToUpdate['activation_status'], $returnedData['activation_status']);

        $this->seeInDatabase('questions', [
            'id' => $dataToUpdate['id'],
            'quiz_id' => $dataToUpdate['quiz_id'],
            'title' => $dataToUpdate['title'],
            'options' => JsonUtility::castToJson($dataToUpdate['options']),
            'activation_status' => $dataToUpdate['activation_status'],
        ]);

        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }
}
