<?php

namespace App\Console\Commands;

use App\Models\Config;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class AutoComfirmOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-confirm';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động phê duyệt đơn hàng đã thanh toán sau một khoảng thời gian cấu hình';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lấy thời gian cấu hình từ bảng configs
        $delayHours = Config::value('order_send_delay_hours') ?? 0;

        if ($delayHours <= 0) {
            $this->info('Không có cấu hình thời gian phê duyệt tự động.');
            return;
        }

        $thresholdTime = Carbon::now()->subHours($delayHours);

        // Chọn đơn hàng đã thanh toán, chưa xác nhận, được tạo trước thời gian quy định
        $orders = Order::where('payment_status', 'completed')
            ->where('status', 'pending') // hoặc trạng thái tương ứng trong hệ thống bạn
            ->where('created_at', '<=', $thresholdTime)
            ->get();

        $count = 0;

        foreach ($orders as $order) {
            $order->status = 'confirmed_pending_production'; // đổi thành trạng thái đã xác nhận
            $order->save();
            $count++;
        }

        logger('Đã chạy autoconfirm lúc ' . now());

        $this->info("Đã tự động xác nhận $count đơn hàng.");
    }
}
