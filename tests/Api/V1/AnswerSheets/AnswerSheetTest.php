<?php

namespace Tests\Api\V1\AnswerSheets;

use App\Consts\AnswerSheetStatus;
use App\Repositories\Contracts\AnswerSheetRepositoryInterface;
use App\Utilities\JsonUtility;
use Hekmatinasser\Verta\Verta;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\Api\Contracts\AnswerSheetMaker;
use Tests\Api\Contracts\Faker;
use Tests\Api\Contracts\QuestionMaker;
use Tests\TestCase;

class AnswerSheetTest extends TestCase
{
    use DatabaseMigrations, Faker, QuestionMaker, AnswerSheetMaker;

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

    public function test_ensure_can_delete_answer_sheet()
    {
        $answerSheet = $this->createAnswerSheet()[0];
        $response = $this->call('DELETE','api/v1/answer-sheets',[
            'id' => $answerSheet->getId()
        ]);

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
        $this->notSeeInDatabase('answer_sheets',[
            'id' => $answerSheet->getId(),
            'quiz_id' => $answerSheet->getQuizId(),
            'answers' => JsonUtility::removeSpacesAndCastToJson($answerSheet->getAnswers()),
            'status' => $answerSheet->getStatus(),
            'score' => $answerSheet->getScore(),
            'finished_at' => $answerSheet->getFinishedAt()->toDateTimeString()
        ]);
    }

    public function test_ensure_can_get_answer_sheets()
    {
        $answerSheetRepository = $this->app->make(AnswerSheetRepositoryInterface::class);

        $answerSheets = $this->createAnswerSheet(100);
        $page = (string)1;
        $pageSize = (string)12;
        $response = $this->call('GET','api/v1/answer-sheets',[
            'page'=> $page,
            'pagesize' => $pageSize
        ]);

        $data = json_decode($response->getContent(), true)['data'];

        $this->assertResponseOk();
        $this->assertLessThanOrEqual($pageSize, count($data));
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

        foreach($data as $returnedAnswerSheet){
            $answerSheetInDatabase = $answerSheetRepository->find($returnedAnswerSheet['id']);
            $this->assertEquals($answerSheetInDatabase->getId(),$returnedAnswerSheet['id']);
            $this->assertEquals($answerSheetInDatabase->getQuizId(),$returnedAnswerSheet['quiz_id']);
            $this->assertEquals($answerSheetInDatabase->getAnswers(),json_decode($returnedAnswerSheet['answers'],true));
            $this->assertEquals($answerSheetInDatabase->getStatus(),$returnedAnswerSheet['status']);
            $this->assertEquals(
                $answerSheetInDatabase->getFinishedAt()->toDateTimeString(),
                Verta::parseFormat('Y/n/j H:i:s',$returnedAnswerSheet['finished_at'])->toCarbon()->toDateTimeString()
            );
        }

    }

    public function test_ensure_can_get_filtered_answer_sheets()
    {
        $answerSheetRepository = $this->app->make(AnswerSheetRepositoryInterface::class);

        $answerSheets = $this->createAnswerSheet(100);
        $page = (string)1;
        $pageSize = random_int(5,10);
        $quizId = (string)$answerSheets[array_rand($answerSheets)]->getQuizId();

        $response = $this->call('GET','api/v1/answer-sheets/get-quiz-answer-sheets',[
            'page'=> $page,
            'pagesize' => $pageSize,
            'quiz_id' => $quizId
        ]);

        $data = json_decode($response->getContent(), true)['data'];

        foreach ($data as $answerSheet) {
            $this->assertEquals($quizId, $answerSheet['quiz_id']);
        }
        $this->assertResponseOk();
        $this->assertLessThanOrEqual($pageSize, count($data));
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);

        foreach($data as $returnedAnswerSheet){
            $answerSheetInDatabase = $answerSheetRepository->find($returnedAnswerSheet['id']);
            $this->assertEquals($answerSheetInDatabase->getId(),$returnedAnswerSheet['id']);
            $this->assertEquals($answerSheetInDatabase->getQuizId(),$returnedAnswerSheet['quiz_id']);
            $this->assertEquals($answerSheetInDatabase->getAnswers(),json_decode($returnedAnswerSheet['answers'],true));
            $this->assertEquals($answerSheetInDatabase->getStatus(),$returnedAnswerSheet['status']);
            $this->assertEquals(
                $answerSheetInDatabase->getFinishedAt()->toDateTimeString(),
                Verta::parseFormat('Y/n/j H:i:s',$returnedAnswerSheet['finished_at'])->toCarbon()->toDateTimeString()
            );
        }

    }
}
