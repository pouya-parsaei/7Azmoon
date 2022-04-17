<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class APIController extends Controller
{
    protected $statusCode;

    public function respondSuccess(string $message, array $data)
    {
        return $this->setStatusCode(200)->respond($message, true, $data);

    }

    public function respondCreated(string $message, array $data)
    {
        return $this->setStatusCode(201)->respond($message, true, $data);

    }

    public function respondNotFound(string $message)
    {
        return $this->setStatusCode(404)->respond($message);

    }

    public function respondInternalError($message)
    {
        return $this->setStatusCode(500)->respond($message);
    }

    private function respond(string $message = '', bool $isSuccess = false, array $data = null)
    {
        $responseData = [
            'success' => $isSuccess,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($responseData,)->setStatusCode($this->getStatusCode());
    }

    private function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    private function getStatusCode()
    {
        return $this->statusCode;
    }

}
