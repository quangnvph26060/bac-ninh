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
        $banks = Bank::pluck('shortName', 'id')->toArray();
        $configPayments = ConfigPayment::query()->latest()->get();
        return view('admin.configuration.payment', compact('banks', 'configPayments'));
    }

    public function updateConfigPayment(Request $request)
    {
        $credentials = $request->validate([
            'accounts' => 'nullable|array',
            'accounts.*.enjoyer' => 'required|max:100',
            'accounts.*.account_number' => 'required|max:100',
            'accounts.*.bank_id' => 'required|max:100'
        ]);

        ConfigPayment::query()->delete();

        if (!empty($credentials['accounts'])) {
            foreach ($credentials['accounts'] as $account) {
                ConfigPayment::create([
                    'enjoyer' => $account['enjoyer'],
                    'account_number' => $account['account_number'],
                    'bank_id' => $account['bank_id'],
                ]);
            }
        }

        return successResponse('Lưu thay đổi thành công.', [], 200, true);
    }
}
