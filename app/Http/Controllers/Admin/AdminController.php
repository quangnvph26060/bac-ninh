<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\AdminService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function getAdminInfor($id)
    {
        $title = 'Thông tin người dùng';
        try {
            $admin = $this->adminService->getUserById($id);
            return view('admin.admin.edit', compact('admin', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to fetch info: ' . $e->getMessage());
            return ApiResponse::error('Failed to fetch info', 500);
        }
    }

    public function updateAdminInfor(Request $request, $id)
    {
        try {
            Log::info("Received request to update admin with ID: $id", $request->all());

            $this->adminService->updateUser($id, $request);

            Log::info("Successfully updated admin with ID: $id");

            session()->flash('success', 'Thay đổi thông tin thành công');

            return redirect()->back();
        } catch (Exception $e) {
            Log::error('Failed to update admin info: ' . $e->getMessage());
            return ApiResponse::error('Failed to update admin info', 500);
        }
    }
    public function changePassword(Request $request)
    {
        if ($request->session()->has('authUser')) {
            $id = session('authUser')->id;
            $result = $this->adminService->changePassword(
                $id,
                $request->password,
                $request->newPassword,
                $request->confirmPassword
            );

            // Return JSON response
            return response()->json($result);
        }

        // Return error if session doesn't have authUser
        return response()->json(['status' => 'error', 'message' => 'Unauthorized access'], 401);
    }
}
