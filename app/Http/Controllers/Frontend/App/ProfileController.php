<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function profile()
    {
        $account = auth()->user();

        return view('frontend.app.profile', compact('account'));
    }

    public function update(Request $request)
    {
        $payloads = $request->validate(
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
                'phone' => 'nullable|regex:/^\+?[1-9]\d{1,14}$/',
                'gender' => 'nullable|in:male,female,other',
                'day_of_birth' => 'nullable|date|date_format:d-m-Y|before_or_equal:today',
                'address' => 'nullable|string|max:255',
                'img_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ],
            __('request.messages'),
            [
                'name' => 'Họ tên',
                'email' => 'Email',
                'phone' => 'Số điện thoại',
                'gender' => 'Giới tính',
                'day_of_birth' => 'Ngày sinh',
                'address' => 'Địa chỉ',
                'img_url' => 'Avatar'
            ]
        );

        if (!$user = User::query()->find(auth()->id())) {
            return errorResponse('Không tìm thấy thông tin khác hàng!', 404);
        }

        $oldAvata = $user->avatar;

        if ($request->hasFile('img_url')) {
            $payloads['img_url'] = uploadImages('img_url', 'avatar');
        }

        $user->update($payloads);

        if (!empty($payloads['img_url'])) deleteImage($oldAvata);

        $updatedUser = User::find($user->id);

        auth()->setUser($updatedUser);

        return successResponse('Cập nhật thông tin thành công.', [
            'image' => showImage($payloads['img_url'] ?? $oldAvata),
            'name' => $user->name
        ], 200, true);
    }

    public function changePassword(Request $request)
    {
        $payloads = $request->validate(
            [
                'old_password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'new_password' => [
                    'required',
                    'different:old_password',
                    Password::min(8)->letters()->mixedCase()->numbers()->symbols()
                ],
            ],
            __('request.messages'),
            [
                'old_password' => 'Mật khẩu cũ',
                'new_password' => 'Mật khẩu mới'
            ]
        );

        if (!$user = User::query()->find(auth()->id())) {
            return errorResponse('Không tìm thấy thông tin khác hàng!', 404);
        }

        if (!Hash::check($payloads['old_password'], $user->password)) {
            return errorResponse('Mật khẩu cũ không đúng!', 422);
        }

        $user->password = $request->new_password;

        $user->save();

        return successResponse('Đổi mật khẩu thành công.');
    }
}
