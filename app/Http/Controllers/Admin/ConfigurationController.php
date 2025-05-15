<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Config;
use App\Models\ConfigPayment;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function configuration()
    {
        return view('admin.configuration.index');
    }

    public function updateConfiguration(Request $request)
    {
        $credentials = $request->validate(
            [
                'title'             => 'nullable|string|max:255',
                'company'           => 'nullable|string|max:255',
                'logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'favicon'           => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
                'address'           => 'nullable|string|max:255',
                'email'             => 'nullable|email|max:255',
                'hotline'           => 'nullable|string|max:15',
                'groups'            => 'nullable|string|max:255',
                'facebook'          => 'nullable|url|max:255',
                'youtobe'           => 'nullable|url|max:255',
                'tiktok'            => 'nullable|url|max:255',
                'copyright'         => 'nullable|string|max:255',
                'seo_title'         => 'nullable|string|max:255',
                'seo_description'   => 'nullable|string|max:500',
            ],
            __('request.messages'),
            [
                'title'             => 'Tiêu đề',
                'company'           => 'Công ty',
                'logo'              => 'Logo',
                'favicon'           => 'Favicon',
                'address'           => 'Địa chỉ',
                'email'             => 'Email',
                'hotline'           => 'Hotline',
                'groups'            => 'Nhóm',
                'facebook'          => 'Facebook',
                'youtobe'           => 'YouTube',
                'tiktok'            => 'TikTok',
                'copyright'         => 'Bản quyền',
                'seo_title'         => 'Tiêu đề SEO',
                'seo_description'   => 'Mô tả SEO',
            ]
        );


        try {
            $config = Config::query()->first();
            $oldLogo = $config->logo;
            $oldFavicon = $config->favicon;

            if ($request->hasFile('logo')) {
                $credentials['logo'] = uploadImages('logo', 'logo');
            }

            if ($request->hasFile('favicon')) {
                $credentials['favicon'] = uploadImages('favicon', 'favicon');
            }

            $config->update($credentials);

            if (!empty($credentials['logo'])) {
                deleteImage($oldLogo);
            }

            if (!empty($credentials['favicon'])) {
                deleteImage($oldFavicon);
            }

            return successResponse('Lưu thay đổi thành công.', [], 200, true);
        } catch (\Exception $e) {
            logger($e->getMessage());

            if (!empty($credentials['logo'])) {
                deleteImage($credentials['logo']);
            }

            if (!empty($credentials['favicon'])) {
                deleteImage($credentials['favicon']);
            }

            return errorResponse('Đã có lỗi xảy ra. Vui lòng thử lại sau!', true);
        }
    }

    public function payment()
    {
        // return "<img src='https://img.vietqr.io/image/970422-5885128062004-compact.png?amount=500000&addInfo=AASNNA' />";
        // $banks = Bank::pluck('shortName', 'id')->toArray();


        $configPayments = ConfigPayment::query()->latest()->get();

        return view('admin.configuration.payment', compact('configPayments'));
    }

    public function getConfigPayment(string $id)
    {
        $configPayment = ConfigPayment::query()->find($id);

        if (!$configPayment) {
            return errorResponse('Không tìm thấy dữ liệu.', true);
        }

        $configPayment->image = showImage($configPayment->image);

        return successResponse('Lấy dữ liệu thành công.', $configPayment, 200, true);
    }

    public function saveConfigPayment(Request $request)
    {
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
            $item->image = showImage($item->image);
            return $item;
        });

        return successResponse('Lưu thay đổi thành công.', $configPayments, 200, true);
    }

    public function updateConfigPaymentStatus(Request $request)
    {
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
        $credentials = $request->validate([
            'id' => 'required|exists:config_payments,id'
        ]);

        ConfigPayment::query()->where('id', $credentials['id'])->delete();

        $configPayments = ConfigPayment::query()->latest()->get()->map(function ($item) {
            $item->image = showImage($item->image);
            return $item;
        });

        return successResponse('Xóa thành công.', $configPayments, 200, true);
    }
}
