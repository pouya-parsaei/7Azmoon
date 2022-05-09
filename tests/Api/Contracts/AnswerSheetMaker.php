<?php

namespace Tests\Api\Contracts;

use App\Consts\AnswerSheetStatus;
use App\Repositories\Contracts\AnswerSheetRepositoryInterface;
use Hekmatinasser\Verta\Verta;

trait AnswerSheetMaker
{
    use QuizMaker;

    private function createAnswerSheet($count = 1)
    {

        $answerSheetRepository = $this->app->make(AnswerSheetRepositoryInterface::class);

        $quizzes = $this->createQuiz(10);


        foreach (range(0, $count) as $item) {
            $answerSheets = [];

                $jalaliDate = Verta::parseFormat('Y/n/j H:i:s',
                    '1' . rand(390, 410)  . '/' .
                    str_pad(rand(01,12),2,0,STR_PAD_LEFT) . '/' .
                    str_pad(rand(01,29),2,0,STR_PAD_LEFT) . ' ' .
                    str_pad(rand(01,11),2,0,STR_PAD_LEFT) . ':' .
                    str_pad(rand(01,59),2,0,STR_PAD_LEFT) . ':' .
                    str_pad(rand(01,59),2,0,STR_PAD_LEFT)
                );
            $answerSheetData = [
                'quiz_id' => (string)$quizzes[array_rand($quizzes)]->getId(),
                'answers' => json_encode([
                    1 => random_int(1, 4),
                    2 => random_int(1, 4),
                    3 => random_int(1, 4),
                    4 => random_int(1, 4),
                ]),
                'status' => array_rand([AnswerSheetStatus::REJECTED, AnswerSheetStatus::PASSED]),
                'score' => array_rand([NULL, random_int(1, 20)]),
                'finished_at' => $jalaliDate->toCarbon()->toDateTimeString()
            ];
            $answerSheets[] = $answerSheetRepository->store($answerSheetData);
        }

        return $answerSheets;
    }
}
