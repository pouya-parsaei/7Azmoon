<?php

namespace App\Entities\Question;

use App\Models\Question;

class QuestionEloquentEntity implements QuestionEntity
{

    public function __construct(private Question $question)
    {

    }

    public function getId(): int
    {
        return $this->question->id;
    }

    public function getQuizId(): int
    {
        return $this->question->quiz_id;
    }

    public function getTitle(): string
    {
        return $this->question->title;
    }

    public function getOptions(): array
    {
        return json_decode($this->question->options,true);
    }

    public function getActivationStatus(): int
    {
        return $this->question->activation_status;
    }

    public function getScore(): int
    {
        return $this->question->score;
    }
}
