<?php

namespace App\Repositories\Contracts;

interface QuestionRepositoryInterface extends RepositoryInterface
{
    public function getQuizQuestions(int $page, int $pageSize = 20, string $search = null, int $quizId, array $columns = []): array;
}
