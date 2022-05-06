<?php

namespace App\Entities\Quiz;

use phpDocumentor\Reflection\Types\Boolean;

interface QuizEntity
{
    public function getId():int;
    public function getCategoryId():int;
    public function getTitle():string;
    public function getDescription():string;
    public function getStartDate():string;
    public function getDuration():int;
    public function getIsActive():bool;
}
