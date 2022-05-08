<?php

namespace App\Repositories\Eloquent;

use App\Entities\Question\QuestionEloquentEntity;
use App\Entities\Question\QuestionEntity;
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

    public function update(int $id,array $data): QuestionEntity
    {
        return !parent::update($id, $data) ? throw new \Exception('خطا در بروزرسانی اطلاعات') :
            new QuestionEloquentEntity(parent::find($id));
    }

    public function getQuizQuestions(int $page, int $pageSize = 20, string $search = null, int $quizId, array $columns = []): array
    {
        return $this->model::when(!is_null($search), function ($query) use ($search, $columns) {
            foreach ($columns as $column) {
                return $query->orWhere($column, $search);
            }
        })->when(!is_null($quizId), function ($query) use ($quizId) {
            return $query->where('quiz_id', $quizId);
        })->paginate($pageSize, $columns, null, $page)->toArray();
    }
}
