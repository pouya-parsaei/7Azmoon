<?php

namespace App\Http\Controllers\Api\V1;

use App\Consts\AnswerSheetStatus;
use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\AnswerSheetRepositoryInterface;
use App\Utilities\JalaliDateConverter;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnswerSheetController extends APIController
{
    public function __construct(private AnswerSheetRepositoryInterface $answerSheetRepository)
    {

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'quiz_id' => ['required', 'numeric', Rule::exists('quizzes','id')],
            'answers' => ['required', 'json'],
            'status' => ['required','numeric'],
            'score' => ['nullable','numeric'],
            'finished_at' => ['required','date']
        ]);
        $answerData = array_merge($request->all(),
            ['finished_at' => JalaliDateConverter::convertToGregorianDateTimeFormat($request->finished_at)]
        );

        $createdAnswerSheet = $this->answerSheetRepository->store($answerData);
        if(!$createdAnswerSheet)
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
}
