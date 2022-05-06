<?php

namespace App\Entities\Quiz;

use App\Models\Quiz;
use phpDocumentor\Reflection\Types\Boolean;

class QuizEloquentEntity implements QuizEntity
{

    public function __construct(private Quiz $quiz)
    {

    }

    public function getId(): int
    {
        return $this->quiz->id;
    }

    public function getCategoryId(): int
    {
        return $this->quiz->category_id;
    }

    public function getTitle(): string
    {
        return $this->quiz->title;
    }

    public function getDescription(): string
    {
        return $this->quiz->description;
    }

    public function getStartDate(): string
    {
        return $this->quiz->start_date;
    }

    public function getDuration(): int
    {
        return $this->quiz->duration;
    }

    public function getIsActive(): bool
    {
        return (bool)$this->quiz->is_active;
    }
}
