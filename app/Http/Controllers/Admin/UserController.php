<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\AdminService;
use App\Services\StorageService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class UserController extends Controller
{
    protected $userService;
    protected $adminService;
    protected $storageService;
    public function __construct(UserService $userService, AdminService $adminService, StorageService $storageService)
    {
        $this->userService = $userService;
        $this->adminService = $adminService;
        $this->storageService = $storageService;
    }

    public function getUserByRole($role)
    {
        try {
            $user = $this->userService->getUserByRole($role);
            // return view('', compact('user'));
        } catch (Exception $e) {
            Log::error('Failed to fetch products: ' . $e->getMessage());
            return ApiResponse::error('Failed to fetch products', 500);
        }
    }

    public function index()
    {
        try {
            $title = "Nhân viên";
            $user = $this->adminService->getStaff();
            // dd($user);
            if (request()->ajax()) {
                $view = view('admin.employee.table', compact('user'))->render();
                return response()->json(['success' => true, 'table' => $view]);
            }
            return view('admin.employee.index', compact('user', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to fetch products: ' . $e->getMessage());
            return ApiResponse::error('Failed to fetch products', 500);
        }
    }
    public function findByPhone(Request $request)
    {
        try {
            $title = "Nhân viên";
            $staff = $this->adminService->findStaffByPhone($request->input('phone'));
            $user = new LengthAwarePaginator(
                $staff ? [$staff] : [],
                $staff ? 1 : 0,
                10,
                1,
                ['path' => Paginator::resolveCurrentPath()]
            );
            return view('admin.employee.index', compact('user', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to find staff: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to find staff'], 500);
        }
    }
    public function edit($id)
    {
        $title = "Sửa nhân viên";
        try {
            $storage = $this->storageService->getAllStorage();
            $user = $this->adminService->getUserById($id);
            return view('admin.employee.edit', compact('user', 'title', 'storage'));
        } catch (Exception $e) {
            Log::error('Failed to find user: ' . $e->getMessage());
            return ApiResponse::error('Failed to find user', 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $user = $this->adminService->updateUser($id, $request->all());
            return redirect()->route('admin.staff.store')->with('success', 'Update thành công');
        } catch (Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            return ApiResponse::error('Failed to update user', 500);
        }
    }

    public function updateadmin(Request $request, $id)
    {
        try {
            $user = $this->adminService->updateUser($id, $request->all());
            $request->session()->regenerate();
            Auth::setUser($user);
            $request->session()->put('authUser', $user);
            return redirect()->route('admin.staff.store')->with('success', 'Update thành công');
        } catch (Exception $e) {
            Log::error('Failed to fetch products: ' . $e->getMessage());
            return ApiResponse::error('Failed to fetch products', 500);
        }
    }

    public function addForm()
    {
        $title = 'Thêm nhân viên';
        $storage = $this->storageService->getAllStorage();
        return view('admin.employee.add', compact('title', 'storage'));
    }
    public function add(Request $request)
    {
        try {
            $user = $this->adminService->addStaff($request->all());
            return redirect()->route('admin.staff.store')->with('success', 'Thêm thành công');
        } catch (Exception $e) {
            Log::error('Failed to add staff: ' . $e->getMessage());
            return ApiResponse::error('Failed to add staff:', 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->adminService->deleteStaff($id);
            $user = User::orderByDesc('created_at')->paginate(10); // Adjust this if you have specific filtering
            $table = view('admin.employee.table', compact('user'))->render();
            $pagination = $user->links('vendor.pagination.custom')->render();

            return response()->json([
                'success' => true,
                'message' => 'Xóa nhân viên thành công',
                'table' => $table,
                'pagination' => $pagination
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete staff: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa nhân viên'
            ]);
        }
    }
}
