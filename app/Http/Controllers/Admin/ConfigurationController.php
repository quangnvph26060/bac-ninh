<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Config;
use App\Models\ConfigPayment;
use App\Models\OrderImportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends Controller
{
    public function configuration()
    {
        $this->authorize('view', Config::class);

        $fileUpload = OrderImportFile::query()->firstOrCreate();

        return view('admin.configuration.index', compact('fileUpload'));
    }

    public function updateConfiguration(Request $request)
    {
        $this->authorize('edit', Config::class);

        $credentials = $request->validate(
            [
                'title' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                'address' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'hotline' => 'nullable|string|max:15',
                'groups' => 'nullable|string|max:255',
                'facebook' => 'nullable|url|max:255',
                'youtobe' => 'nullable|url|max:255',
                'tiktok' => 'nullable|url|max:255',
                'copyright' => 'nullable|string|max:255',
                'seo_title' => 'nullable|string|max:255',
                'seo_description' => 'nullable|string|max:500',
                'tax_rate' => 'nullable|numeric|min:0|regex:/^\d*(\.\d{1,2})?$/',
                'order_send_delay_hours' => 'nullable|numeric|min:0',
                'custom_order_send_delay_hours' => 'nullable|numeric|min:0',
                'sample_file_path' => 'nullable|mimes:xlsx,xls|max:10240',
                'data_file_path' => 'nullable|mimes:xlsx,xls|max:10240'
            ],
            __('request.messages'),
            [
                'title' => 'Tiêu đề',
                'company' => 'Công ty',
                'logo' => 'Logo',
                'favicon' => 'Favicon',
                'address' => 'Địa chỉ',
                'email' => 'Email',
                'hotline' => 'Hotline',
                'groups' => 'Nhóm',
                'facebook' => 'Facebook',
                'youtobe' => 'YouTube',
                'tiktok' => 'TikTok',
                'copyright' => 'Bản quyền',
                'seo_title' => 'Tiêu đề SEO',
                'seo_description' => 'Mô tả SEO',
            ]
        );

        DB::beginTransaction();
        try {
            $config = Config::query()->firstOrFail();
            $fileUpload = OrderImportFile::query()->firstOrCreate();

            // Lưu đường dẫn file cũ để xóa khi upload mới
            $oldLogo = $config->logo;
            $oldFavicon = $config->favicon;
            $oldSampleFile = $fileUpload->sample_file_path;
            $oldDataFile = $fileUpload->data_file_path;

            $newLogo = $newFavicon = $newSampleFile = $newDataFile = null;

            if ($request->hasFile('logo')) {
                $newLogo = uploadImages('logo', 'logo');
                $credentials['logo'] = $newLogo;
            }

            if ($request->hasFile('favicon')) {
                $newFavicon = uploadImages('favicon', 'favicon');
                $credentials['favicon'] = $newFavicon;
            }

            if ($request->hasFile('sample_file_path')) {
                $newSampleFile = uploadExcel('sample_file_path', 'order_excels');
            }

            if ($request->hasFile('data_file_path')) {
                $newDataFile = uploadExcel('data_file_path', 'order_excels');
            }

            if (!empty($credentials['custom_order_send_delay_hours'])) {
                $credentials['order_send_delay_hours'] = $credentials['custom_order_send_delay_hours'];
            }

            $config->update($credentials);

            $fileUpload->update([
                'sample_file_path' => $newSampleFile ?? $oldSampleFile,
                'data_file_path' => $newDataFile ?? $oldDataFile,
                'updated_at_sample' => $newSampleFile ? now() : $fileUpload->updated_at_sample,
                'updated_at_data' => $newDataFile ? now() : $fileUpload->updated_at_data,
            ]);

            DB::commit();

            // Sau khi commit DB thành công, xóa file cũ
            if ($newLogo && $oldLogo) {
                deleteImage($oldLogo);
            }
            if ($newFavicon && $oldFavicon) {
                deleteImage($oldFavicon);
            }
            if ($newSampleFile && $oldSampleFile) {
                deleteImage($oldSampleFile);
            }
            if ($newDataFile && $oldDataFile) {
                deleteImage($oldDataFile);
            }

            return successResponse('Lưu thay đổi thành công.', [], 200, true);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());

            // Xóa các file mới nếu có upload nhưng lỗi
            if ($newLogo) {
                deleteImage($newLogo);
            }
            if ($newFavicon) {
                deleteImage($newFavicon);
            }
            if ($newSampleFile) {
                deleteImage($newSampleFile);
            }
            if ($newDataFile) {
                deleteImage($newDataFile);
            }

            return errorResponse('Đã có lỗi xảy ra. Vui lòng thử lại sau!', true);
        }
    }


    public function payment()
    {
        // return "<img src='https://img.vietqr.io/image/970422-5885128062004-compact.png?amount=500000&addInfo=AASNNA' />";
        // $banks = Bank::pluck('shortName', 'id')->toArray();
        $this->authorize('view', ConfigPayment::class);


        $configPayments = ConfigPayment::query()->latest()->get();

        return view('admin.configuration.payment', compact('configPayments'));
    }

    public function getConfigPayment(string $id)
    {
        $this->authorize('view', ConfigPayment::class);

        $configPayment = ConfigPayment::query()->find($id);

        if (!$configPayment) {
            return errorResponse('Không tìm thấy dữ liệu.', true);
        }

        $configPayment->image = showImage($configPayment->image);

        return successResponse('Lấy dữ liệu thành công.', $configPayment, 200, true);
    }

    public function saveConfigPayment(Request $request)
    {
        $this->authorize('save', ConfigPayment::class);

        $credentials = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'id' => 'nullable|exists:config_payments,id'
        ]);

        if ($request->hasFile('image')) {
            $credentials['image'] = uploadImages('image', 'image');
        }

        if (!empty($credentials['id'])) {
            $configPayment = ConfigPayment::query()->find($credentials['id']);
            if ($request->hasFile('image') && !empty($oldImage = $configPayment->image)) {
                deleteImage($oldImage);
            }
            $configPayment->update($credentials);
        } else {
            ConfigPayment::query()->create($credentials);
        }

        $configPayments = ConfigPayment::query()->latest()->get()->map(function ($item) {
            $item['image'] = showImage($item->image);
            return $item;
        });

        return successResponse('Lưu thay đổi thành công.', $configPayments, 200, true);
    }

    public function updateConfigPaymentStatus(Request $request)
    {
        $this->authorize('editStatus', ConfigPayment::class);

        $credentials = $request->validate([
            'id' => 'required|exists:config_payments,id',
            'status' => 'required|boolean'
        ]);

        $configPayment = ConfigPayment::query()->find($credentials['id']);

        $configPayment->status = $credentials['status'];
        $configPayment->save();

        return successResponse('Cập nhật trạng thái thành công.', [], 200, true);
    }

    public function destroyConfigPayment(Request $request)
    {
        $this->authorize('destroy', ConfigPayment::class);

        $credentials = $request->validate([
            'id' => 'required|exists:config_payments,id'
        ]);

        ConfigPayment::query()->where('id', $credentials['id'])->delete();

        $configPayments = ConfigPayment::query()->latest()->get()->map(function ($item) {
            $item['image'] = showImage($item->image);
            return $item;
        });

        return successResponse('Xóa thành công.', $configPayments, 200, true);
    }
}
