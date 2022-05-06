<?php

namespace App\Entities\Question;

interface QuestionEntity
{
    public function getId():int;
    public function getQuizId():int;
    public function getTitle():string;
    public function getOptions():array;
    public function getScore():int;
    public function getActivationStatus():int;
}
