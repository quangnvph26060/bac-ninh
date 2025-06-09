<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use Illuminate\Support\Carbon;

class AutoCloseTickets extends Command
{
    protected $signature = 'tickets:autoclose';

    protected $description = 'Tự động đóng ticket sau 3 ngày nếu không có phản hồi';

    public function handle()
    {
        // Lấy thời điểm 3 ngày trước
        $threeDaysAgo = Carbon::now()->subDays(3);

        // Lấy các ticket đủ điều kiện đóng
        $tickets = Ticket::where('status', 'resolved')
            ->where('is_confirmed', false)
            ->whereDoesntHave('messages', function ($query) use ($threeDaysAgo) {
                $query->where('created_at', '>', $threeDaysAgo);
            })
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->update(['status' => 'closed']);
        }

        $this->info('AutoCloseTicketsJob dispatched!');
    }
}
