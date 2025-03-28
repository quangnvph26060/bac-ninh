<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Storage;
use App\Services\StorageService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

class StorageController extends Controller
{
    protected $storageService;
    public function __construct(StorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    public function index()
    {
        $title = 'Kho hàng';
        try {
            $storages  = $this->storageService->getPaginatedStorage();
            return view('admin.storage.index', compact('title', 'storages'));
        } catch (Exception $e) {
            Log::error("Failed to fetch Storages: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch Storages'], 500);
        }
    }
    public function getProductInStorage()
    {
    }
    public function findStorageByName(Request $request)
    {
        try {
            $storages = $this->storageService->findStorageByName($request->name);
            $storages = new LengthAwarePaginator(
                $storages,
                $storages->count(),
                10,
                LengthAwarePaginator::resolveCurrentPage(),
                ['path' => Paginator::resolveCurrentPath()]
            );

            if ($request->ajax()) {
                return response()->json([
                    'table' => view('admin.storage.table', ['storages' => $storages])->render(),
                    'pagination' => $storages->appends($request->except('page'))->links('vendor.pagination.custom')->toHtml()
                ]);
            }

            return view('admin.storage.index', compact('storages'));
        } catch (Exception $e) {
            Log::error('Failed to find storage: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to find Storage'], 500);
        }
    }
    public function edit($id)
    {
        $title = "Sửa thông tin kho hàng";
        try {
            $storages = $this->storageService->getStorageById($id);
            return view('admin.storage.edit', compact('title', 'storages'));
        } catch (Exception $e) {
            Log::error('Failed to find Storage: ' . $e->getMessage());
        }
    }
    public function add()
    {
        $title = "Thêm kho hàng";
        return view('admin.storage.add', compact('title'));
    }

    public function create(Request $request)
    {
        $storages = $this->storageService->addStorage($request->all());
        return redirect()->route('admin.storage.index')->with('success', 'Thêm kho hàng thành công');
    }
    public function update($id, Request $request)
    {
        try {
            $storages = $this->storageService->updateStorage($id, $request->all());
            session()->flash('success', 'Cập nhật thông tin kho hàng thành công');
            return redirect()->route('admin.storage.index');
        } catch (Exception $e) {
            Log::error('Failed to update Storage: ' . $e->getMessage());
            return ApiResponse::error('Failed to update storage', 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->storageService->deleteStorage($id);
            $storages = Storage::orderByDesc('created_at')->paginate(10);
            $view = view('admin.storage.table', compact('storages'))->render();
            return response()->json(['success' => true, 'message' => 'Xóa kho hàng thành công', 'table' => $view]);
        } catch (Exception $e) {
            Log::error('Failed to delete Storage: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Không thể xóa kho hàng']);
        }
    }

    public function detail($id)
    {
        try {
            $storage = $this->storageService->getStorageById($id);
            $product = $this->storageService->getProductInStorage($id);
            return view('admin.storage.detail', compact('product', 'storage'));
        } catch (Exception $e) {
            Log::error('Failed to find Storage info: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch Storage info'], 500);
        }
    }
}
