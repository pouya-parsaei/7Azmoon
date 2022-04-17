<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;

class UserController extends APIController
{

    public function __construct(private UserRepositoryInterface $userRepository)
    {

    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'full_name' => 'required|string|min:2|max:128',
            'email' => 'required|email',
            'mobile' => 'required|digits:11',
            'password' => 'required|min:6|max:128'
        ]);


        $this->userRepository->store([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => $request->password,
            'role' => 'user'
        ]);

        return $this->respondCreated('کاربر با موفقیت ایجاد شد.',[
        'full_name' => $request->full_name,
        'email' => $request->email,
        'mobile' => $request->mobile,
        'password' => $request->password,
        'role' => 'user'
    ]);
    }
}
