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
}
