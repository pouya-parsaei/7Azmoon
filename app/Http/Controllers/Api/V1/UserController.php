<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function store()
    {
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
