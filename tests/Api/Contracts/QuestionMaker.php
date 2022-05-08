<?php

namespace Tests\Api\Contracts;

use App\Consts\QuestionStatus;
use App\Repositories\Contracts\QuestionRepositoryInterface;

trait QuestionMaker
{
    use QuizMaker;

    private function createQuestion($count = 1)
    {
        $this->setUpFaker();

        $questionRepository = $this->app->make(QuestionRepositoryInterface::class);

        $quizzes = $this->createQuiz(10);


        foreach (range(0, $count) as $item) {
            $options = [
                1 => ['text' => $this->faker->sentence, 'is_correct' => 0],
                2 => ['text' => $this->faker->sentence, 'is_correct' => 1],
                3 => ['text' => $this->faker->sentence, 'is_correct' => 0],
                4 => ['text' => $this->faker->sentence, 'is_correct' => 0]
            ];
            shuffle($options);

            $questions = [];

            $questionData = [
                'quiz_id' => (string)$quizzes[array_rand($quizzes)]->getId(),
                'title' => $this->faker->sentence,
                'options' => json_encode($options),
                'score' => random_int(1, 10),
                'activation_status' => QuestionStatus::ACTIVE
            ];

            $questions[] = $questionRepository->store($questionData);
        }
        return $questions;
    }
}
