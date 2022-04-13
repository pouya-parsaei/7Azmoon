<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserController extends Controller
{

    public function __construct(private UserRepositoryInterface $userRepository)
    {

    }
    public function store()
    {
        $this->userRepository->store([
            'full_name' => 'Pouya Parsaei',
            'email' => 'pya.prs@gmail.com',
            'mobile' => '09121112222',
            'password' => '123456'
        ]);
        return response()->json([
            'success' => true,
            'message' => 'کاربر با موفقیت ایجاد شد.',
            'data' => [
                'full_name' => 'Pouya Parsaei',
                'email' => 'pya.prs@gmail.com',
                'mobile' => '09121112222',
                'password' => '123456'
            ]
        ])->setStatusCode(201);
    }
}
