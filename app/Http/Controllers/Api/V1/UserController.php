<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\APIController;
use App\Repositories\Contracts\UserRepositoryInterface;
use http\Exception\RuntimeException;
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


        $createdUser = $this->userRepository->store([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => app('hash')->make($request->password),

        ]);

        return $this->respondCreated('کاربر با موفقیت ایجاد شد.', [
            'full_name' => $createdUser->getFullName(),
            'email' => $createdUser->getEmail(),
            'mobile' => $createdUser->getMobile(),
            'password' => $createdUser->getPassword(),
        ]);
    }

    public function updateInfo(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'full_name' => 'required|string|min:2|max:128',
            'email' => 'required|email',
            'mobile' => 'required|digits:11'
        ]);

        try{
        $updatedUser = $this->userRepository->update($request->id, [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'mobile' => $request->mobile
        ]);
        }catch (\Exception $e){
            return $this->respondInternalError('خطا در بروزرسانی اطلاعات');
        }
        return $this->respondSuccess('اطلاعات کاربر با موفقیت بروزرسانی گردید.', [
            'full_name' => $updatedUser->getFullName(),
            'email' => $updatedUser->getEmail(),
            'mobile' => $updatedUser->getMobile()
        ]);
    }

    public function updatePassword(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|numeric',
            'password' => 'min:6|required_with:password_repeat|same:password_repeat',
            'password_repeat' => 'min:6'
        ]);

        try {
            $updatedUser = $this->userRepository->update($request->id, [
                'password' => app('hash')->make($request->password)
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطا در بروزرسانی');
        }

        return $this->respondSuccess('رمز عبور با موفقیت بروزرسانی گردید.', [
            'full_name' => $updatedUser->getFullName(),
            'email' => $updatedUser->getFullName(),
            'mobile' => $updatedUser->getFullName()
        ]);
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'search' => 'nullable|string',
            'page' => 'required|numeric',
            'pagesize' => 'nullable|numeric'
        ]);

        $users = $this->userRepository->paginate($request->page, $request->pagesize ?? 20, $request->search ?? null);
        return $this->respondSuccess('لیست کاربران', $users);

    }

    public function test(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);
        $user = $this->userRepository->find($request->id);
        dd($user->getFullName());
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required'
        ]);

        if (!$this->userRepository->find($request->id)) {
            return $this->respondNotFound('کاربر یافت نشد.');
        }

        if (!$this->userRepository->delete($request->id)) {
            return $this->respondInternalError('خطایی رخ داده');
        }
        return $this->respondSuccess('کاربر با موفقیت حذف شد', []);
    }
}
