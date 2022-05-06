<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Utilities\JalaliDateConverter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuizController extends APIController
{
    public function __construct(private QuizRepositoryInterface $quizRepository)
    {

    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'page' => 'required|numeric',
            'pagesize' => 'numeric|nullable',
            'search' => 'string|nullable',
        ]);
        $quizzes = $this->quizRepository->paginate($request->page, $request->pagesize ?? 20,
            $request->search ?? null, ['title', 'description', 'start_date', 'duration']);
        return $this->respondSuccess('لیست آزمون ها', $quizzes['data']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => ['required', 'numeric', Rule::exists('categories', 'id')],
            'title' => 'required|string',
            'description' => 'required|string|min:2|max:2000',
            'start_date' => 'required|string',
            'duration' => 'required|numeric',
        ]);

        $quizData = array_merge($request->all(),
            ['start_date' => JalaliDateConverter::convertToGregorianDateTimeFormat($request->start_date)]
        );

        $quiz = $this->quizRepository->store($quizData);

        return $this->respondCreated('آزمون با موفقیت ایجاد شد.', [
            'category_id' => $quiz->getCategoryId(),
            'title' => $quiz->getTitle(),
            'description' => $quiz->getDescription(),
            'start_date' => $quiz->getStartDate(),
            'duration' => $quiz->getDuration(),
        ]);

    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', Rule::exists('quizzes', 'id')],
            'category_id' => ['required', 'numeric', Rule::exists('categories', 'id')],
            'title' => 'required|string',
            'description' => 'required|string|min:2|max:2000',
            'start_date' => 'required|string',
            'duration' => 'required|numeric',
            'is_active' => 'required|boolean'
        ]);

        $quizData = array_merge($request->all(),
            ['start_date' => JalaliDateConverter::convertToGregorianDateTimeFormat($request->start_date)]
        );

        try {
            $quiz = $this->quizRepository->update($request->id,$quizData);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطا در بروزرسانی اطلاعات');
        }
        return $this->respondSuccess('آزمون با موفقیت بروزرسانی شد.', [
            'category_id' => $quiz->getCategoryId(),
            'title' => $quiz->getTitle(),
            'description' => $quiz->getDescription(),
            'start_date' => $quiz->getStartDate(),
            'duration' => $quiz->getDuration(),
            'is_active' => $quiz->getIsActive()
        ]);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', Rule::exists('quizzes', 'id')],
        ]);

        if (!$this->quizRepository->delete($request->id))
            return $this->respondInternalError('خطا در حذف آزمون');

        return $this->respondSuccess('آزمون حذف شد.', []);
    }
}
