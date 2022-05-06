<?php

namespace App\Repositories\Eloquent;

use App\Entities\Question\QuestionEloquentEntity;
use App\Models\Question;
use App\Repositories\Contracts\QuestionRepositoryInterface;

class EloquentQuestionRepository extends EloquentBaseRepository implements QuestionRepositoryInterface
{
    protected $model = Question::class;

    public function store(array $data)
    {
        $question = parent::store($data);
        return new QuestionEloquentEntity($question);
    }
}
