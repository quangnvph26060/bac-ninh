<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SebastianBergmann\Type\TrueType;

class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $perPage = $request->input('per_page', 10);
        $type = $request->input('type', 'checkbox');

        $photos = Photo::query()
            ->when(
                !empty($search),
                fn($query) =>
                $query->where("name", "like", "%{$search}%")
            )
            ->where('user_id', auth('web')->id())
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            $html = view('frontend.app.photo.list', ['photos' => $photos, 'type' => $type])->render();
            return handleResponse("success", true, 200, $html, false);
        }

        return view('frontend.app.photo.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'artworks' => 'required|array',
            'artworks.*' => 'image|max:10240',
        ]);

        $uploadedFiles = $request->file('artworks');
        $savedPhotos = [];

        foreach ($uploadedFiles as $file) {
            try {
                $originalName = $file->getClientOriginalName();

                // Kiểm tra trùng tên trong DB
                if (Photo::where('name', $originalName)->exists()) {
                    continue; // Bỏ qua
                }

                // Lưu ảnh vào thư mục với tên UUID
                $directory = 'uploads/photos';
                $storagePath = storage_path('app/public/' . $directory);

                if (!file_exists($storagePath)) {
                    mkdir($storagePath, 0777, true);
                }

                $extension = $file->getClientOriginalExtension();
                $uuidName = Str::uuid()->toString() . '.' . $extension;
                $destination = $storagePath . '/' . $uuidName;

                $file->move($storagePath, $uuidName);

                // Lấy thông tin ảnh
                $info = getImageInfo($destination);

                $photo = Photo::create([
                    'user_id' => auth('web')->id(),
                    'name'    => $originalName,                 // Giữ nguyên tên gốc
                    'path'    => $directory . '/' . $uuidName,  // Đường dẫn sử dụng UUID
                    'width'   => $info['width'],
                    'height'  => $info['height'],
                    'ppi'     => $info['x_dpi'],
                    'format'  => $info['format'],
                ]);

                $savedPhotos[] = $photo;
            } catch (\Exception $e) {
                continue;
            }
        }

        return response()->json([
            'success' => count($savedPhotos) > 0,
            'message' => count($savedPhotos) > 0 ? 'Upload thành công!' : 'Không có ảnh nào được upload.',
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Không có ảnh nào được chọn.']);
        }

        $photos = Photo::whereIn('id', $ids)->get();

        foreach ($photos as $photo) {
            // Xoá file
            deleteImage($photo->path);

            // Xoá bản ghi
            $photo->delete();
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa ảnh thành công.']);
    }
}
