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

    public function index(Request $request)
    {
        $this->validate($request, [
            'page' => ['required', 'numeric'],
            'pagesize' => ['numeric', 'nullable'],
            'search' => ['string', 'nullable'],
            'quiz_id' => ['numeric', 'nullable'],
        ]);

        $questions = $this->questionRepository->paginate($request->page, $request->pagesize ?? 20, $request->search ?? null,
            ['quiz_id', 'title', 'options', 'score', 'activation_status']);

        return $this->respondSuccess('سوالات', $questions);

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

        $createdQuestion = $this->questionRepository->store([
            'quiz_id' => $request->quiz_id,
            'title' => $request->title,
            'options' => json_encode($request->options),
            'score' => $request->score,
            'activation_status' => $request->activation_status
        ]);

        return $this->respondCreated('سوال با موفقیت ایجاد شد.', [
            'id' => $createdQuestion->getId(),
            'quiz_id' => $createdQuestion->getQuizId(),
            'title' => $createdQuestion->getTitle(),
            'options' => $createdQuestion->getOptions(),
            'score' => $createdQuestion->getScore(),
            'activation_status' => $createdQuestion->getActivationStatus()
        ]);

    }

    public function getQuizQuestions(Request $request)
    {
        $this->validate($request, [
            'quiz_id' => ['required', 'numeric', Rule::exists('quizzes', 'id')]
        ]);
        $questions = $this->questionRepository->getQuizQuestions($request->page, $request->pagesize ?? 20, $request->search ?? null,
            $request->quiz_id ?? null,
            [
                'quiz_id', 'title', 'options', 'score', 'activation_status'
            ]);

        return $this->respondSuccess('سوالات', $questions);

    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', Rule::exists('questions', 'id')],
            'quiz_id' => ['required', 'numeric', Rule::exists('quizzes', 'id')],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'options' => ['required', 'array'],
            'score' => ['required', 'numeric', 'between:0,99.99'],
            'activation_status' => 'required|numeric'
        ]);

        try {
            $updatedQuestion = $this->questionRepository->update($request->id, [
                'quiz_id' => $request->quiz_id,
                'title' => $request->title,
                'options' => json_encode($request->options),
                'score' => $request->score,
                'activation_status' => $request->activation_status
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطا در بروزرسانی سوال');
        }

        return $this->respondSuccess('سوال بروزرسانی شد.', [
            'id' => $updatedQuestion->getId(),
            'quiz_id' => $updatedQuestion->getQuizId(),
            'title' => $updatedQuestion->getTitle(),
            'options' => $updatedQuestion->getOptions(),
            'score' => $updatedQuestion->getScore(),
            'activation_status' => $updatedQuestion->getActivationStatus()
        ]);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', Rule::exists('questions', 'id')]
        ]);

        if (!$this->questionRepository->delete($request->id))
            return $this->respondInternalError('خطا در حذف سوال');

        return $this->respondSuccess('سوال حذف شذ.', []);
    }

}
