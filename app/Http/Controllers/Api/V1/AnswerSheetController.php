<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\AnswerSheetRepositoryInterface;
use App\Utilities\JalaliDateConverter;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnswerSheetController extends APIController
{
    public function __construct(private AnswerSheetRepositoryInterface $answerSheetRepository)
    {

    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'page' => ['required', 'numeric'],
            'pagesize' => ['nullable', 'numeric'],
            'search' => ['nullable', 'string']
        ]);
        $answerSheets = $this->answerSheetRepository->paginate($request->page, $request->pagesize ?? 20,
            $request->search ?? null, ['id', 'quiz_id', 'answers', 'status', 'score', 'finished_at']);

        $answerSheetsWithConvertedFinishedAt = [];
        foreach ($answerSheets['data'] as $answerSheet) {
            $answerSheet['finished_at'] = Verta::instance($answerSheet['finished_at'])->format('Y/m/j H:i:s');
            $answerSheetsWithConvertedFinishedAt[] = $answerSheet;
        }

        return $this->respondSuccess('لیست پاسخ نامه ها', $answerSheetsWithConvertedFinishedAt);
    }

    public function getQuizAnswerSheets(Request $request)
    {
        $this->validate($request, [
            'page' => ['required', 'numeric'],
            'pagesize' => ['nullable', 'numeric'],
            'search' => ['nullable', 'string'],
            'quiz_id' => ['required', 'numeric', Rule::exists('quizzes', 'id')]
        ]);
        $answerSheets = $this->answerSheetRepository->getQuizAnswerSheets(
            $request->page,
            $request->pagesize ?? 20,
            $request->search ?? null,
            $request->quiz_id ?? null,
            ['id', 'quiz_id', 'answers', 'status', 'score', 'finished_at']
        );

        $answerSheetsWithConvertedFinishedAt = [];
        foreach ($answerSheets['data'] as $answerSheet) {
            $answerSheet['finished_at'] = Verta::instance($answerSheet['finished_at'])->format('Y/m/j H:i:s');
            $answerSheetsWithConvertedFinishedAt[] = $answerSheet;
        }

        return $this->respondSuccess('پاسخ نامه ها', $answerSheetsWithConvertedFinishedAt);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'quiz_id' => ['required', 'numeric', Rule::exists('quizzes', 'id')],
            'answers' => ['required', 'json'],
            'status' => ['required', 'numeric'],
            'score' => ['nullable', 'numeric'],
            'finished_at' => ['required', 'date']
        ]);
        $answerData = array_merge($request->all(),
            ['finished_at' => JalaliDateConverter::convertToGregorianDateTimeFormat($request->finished_at)]
        );

        $createdAnswerSheet = $this->answerSheetRepository->store($answerData);
        if (!$createdAnswerSheet)
            return $this->respondInternalError('خطا در ایجاد پاسخ نامه');
        return $this->respondCreated('آزمون با موفقیت ایجاد شد.', [
            'id' => $createdAnswerSheet->getId(),
            'quiz_id' => $createdAnswerSheet->getQuizId(),
            'answers' => $createdAnswerSheet->getAnswers(),
            'status' => $createdAnswerSheet->getStatus(),
            'score' => $createdAnswerSheet->getScore(),
            'finished_at' => Verta::instance($createdAnswerSheet->getFinishedAt())->format('Y/m/j H:i:s'),
        ]);
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'numeric', Rule::exists('answer_sheets', 'id')]
        ]);

        if (!$this->answerSheetRepository->delete($request->id))
            return $this->respondInternalError('خطا در حذف پاسخ نامه');

        return $this->respondSuccess('پاسخ نامه حذف شد', []);
    }
}
