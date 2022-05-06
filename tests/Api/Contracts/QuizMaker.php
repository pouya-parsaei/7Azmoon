<?php

namespace Tests\Api\Contracts;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\QuizRepositoryInterface;

trait QuizMaker
{
    use CategoryMaker;
    private function createQuiz($count = 1)
    {
        $this->setUpFaker();

        $quizRepository = $this->app->make(QuizRepositoryInterface::class);

        $categories = $this->createCategories(10);

        $quizzes = [];

        foreach (range(0, $count) as $item) {
            $newQuiz = [
                'category_id' => (string)$categories[array_rand($categories)]->getId(),
                'title' => $this->faker->word,
                'description' => $this->faker->sentence,
                'start_date' => '1401/01/31 03:10',
                'duration' => (string)random_int(5, 60),
                'is_active' => array_rand([true,false])
            ];
            $quizzes[] = $quizRepository->store($newQuiz);
        }

        return $quizzes;
    }
}
