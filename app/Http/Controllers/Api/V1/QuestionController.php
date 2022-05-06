<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\QuestionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends APIController
{
    public function __construct(private QuestionRepositoryInterface $questionRepository)
    {

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'quiz_id' => ['required', 'numeric', Rule::exists('quizzes', 'id')],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'options' => ['required', 'array'],
            'score' => ['required', 'numeric', 'between:0,99.99'],
            'activation_status' => 'required|numeric'
        ]);

        $createdQuiz = $this->questionRepository->store([
            'quiz_id' => $request->quiz_id,
            'title' => $request->title,
            'options' => json_encode($request->options),
            'score' => $request->score,
            'activation_status' => $request->activation_status
        ]);

        return $this->respondCreated('سوال با موفقیت ایجاد شد.', [
            'id' => $createdQuiz->getId(),
            'quiz_id' => $createdQuiz->getQuizId(),
            'title' => $createdQuiz->getTitle(),
            'options' => $createdQuiz->getOptions(),
            'score' => $createdQuiz->getScore(),
            'activation_status' => $createdQuiz->getActivationStatus()
        ]);

    }
}
