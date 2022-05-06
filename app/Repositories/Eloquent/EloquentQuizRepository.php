<?php

namespace App\Repositories\Eloquent;


use App\Entities\Quiz\QuizEloquentEntity;
use App\Entities\Quiz\QuizEntity;
use App\Models\Quiz;
use App\Repositories\Contracts\QuizRepositoryInterface;

class EloquentQuizRepository extends EloquentBaseRepository implements QuizRepositoryInterface
{
    protected $model = Quiz::class;

    public function store(array $data): QuizEntity
    {
        $quiz = parent::store($data);

        return new QuizEloquentEntity($quiz);
    }

    public function update(int $id,array $data): QuizEntity
    {
        return !parent::update($id, $data) ? throw new \Exception('خطا در بروزرسانی اطلاعات') :
            new QuizEloquentEntity(parent::find($id));
    }
}
