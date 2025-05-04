<?php

namespace App\Services;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Illuminate\Support\Facades\Cache;

class PaypalService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new PayPalClient();
        $this->provider->setApiCredentials(config('paypal'));

        // Kiểm tra cache token
        $token = Cache::get('paypal_access_token');

        if (!$token) {
            $token = $this->provider->getAccessToken();
            // Lưu token vào cache trong 8 tiếng (hoặc bạn có thể lấy từ response của getAccessToken để xác định thời gian chính xác)
            Cache::put('paypal_access_token', $token, now()->addHours(8));
        }

        $this->provider->setAccessToken($token);
    }

    /**
     * Tạo đơn hàng thanh toán
     */
    public function createOrder($amount, $currency = 'USD', $returnUrl, $cancelUrl)
    {
        $order = [
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => $returnUrl,
                "cancel_url" => $cancelUrl,
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => $currency,
                        "value" => $amount
                    ]
                ]
            ]
        ];

        return $this->provider->createOrder($order);
    }

    /**
     * Xác nhận giao dịch khi người dùng thanh toán xong
     */
    public function captureOrder($token)
    {
        return $this->provider->capturePaymentOrder($token);
    }
}
