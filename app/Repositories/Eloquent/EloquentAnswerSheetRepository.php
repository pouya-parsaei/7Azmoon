<?php

namespace App\Repositories\Eloquent;

use App\Entities\AnswerSheet\AnswerSheetEloquentEntity;
use App\Entities\AnswerSheet\AnswerSheetEntity;
use App\Models\AnswerSheet;
use App\Repositories\Contracts\AnswerSheetRepositoryInterface;

class EloquentAnswerSheetRepository extends EloquentBaseRepository implements AnswerSheetRepositoryInterface
{
    protected $model = AnswerSheet::class;

    public function store(array $data): AnswerSheetEntity
    {
        try{
        $createdAnswerSheet = parent::store($data);
        } catch(\Exception $e){
            return false;
        }
        return new  AnswerSheetEloquentEntity($createdAnswerSheet);
    }

    public function find(int $id):AnswerSheetEntity
    {
        $answerSheet = parent::find($id);
        return new  AnswerSheetEloquentEntity($answerSheet);

    }

    public function getQuizAnswerSheets(int $page, int $pageSize = 20, string $search = null, int $quizId, array $columns = []): array
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
