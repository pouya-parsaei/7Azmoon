<?php

namespace Tests\Api\V1\AnswerSheets;

use App\Consts\AnswerSheetStatus;
use App\Utilities\JsonUtility;
use Hekmatinasser\Verta\Verta;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\Api\Contracts\Faker;
use Tests\Api\Contracts\QuestionMaker;
use Tests\TestCase;

class AnswerSheetTest extends TestCase
{
    use DatabaseMigrations, Faker, QuestionMaker;

    public function test_ensure_can_create_answer_sheet()
    {
        $quiz = $this->createQuiz()[0];
        $jalaliDate = Verta::now();

        $answerSheetData = [
            'quiz_id' => $quiz->getId(),
            'answers' => json_encode([
                1 => 4,
                2 => 2,
                3 => 3,
                4 => 5,
            ]),
            'status' => array_rand([AnswerSheetStatus::REJECTED, AnswerSheetStatus::PASSED]),
            'score' => array_rand([NULL, random_int(1, 20)]),
            'finished_at' => $jalaliDate->format('Y/m/j H:i:s')
        ];

        $response = $this->call('post', 'api/v1/answer-sheets', $answerSheetData);
        $returnedData = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(201, $response->getStatusCode());

        $this->seeInDatabase('answer_sheets', [
            'id' => $returnedData['id'],
            'quiz_id' => $answerSheetData['quiz_id'],
            'answers' => JsonUtility::removeSpacesAndCastToJson($returnedData['answers']),
            'status' => $answerSheetData['status'],
            'score' => $answerSheetData['score'],
            'finished_at' => $jalaliDate->toCarbon()->toDateTimeString(),
        ]);
        $this->assertEquals($answerSheetData['quiz_id'], $returnedData['quiz_id']);
        $this->assertEquals(json_decode($answerSheetData['answers'], true), $returnedData['answers']);
        $this->assertEquals($answerSheetData['status'], $returnedData['status']);
        $this->assertEquals($answerSheetData['score'], $returnedData['score']);
        $this->assertEquals($answerSheetData['finished_at'], $returnedData['finished_at']);


        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'quiz_id',
                'answers',
                'status',
                'score',
                'finished_at'
            ]
        ]);
    }
}
