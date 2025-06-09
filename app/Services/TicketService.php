<?php

namespace App\Services;

use App\Models\Ticket;

class TicketService extends BaseService
{
    public function __construct(Ticket $ticket)
    {
        parent::__construct($ticket);
    }

    public function pagination()
    {

        return $this->queryBuilder(
            ['*'],
            ['user', 'order', 'subject']
        );
    }

    public function show($id)
    {
        return $this->findById($id, ['*'], ['user', 'order', 'subject']);
    }

    public function send($credentials)
    {

        return transaction(function () use ($credentials) {
            $ticket = $this->findById($credentials['ticket_id']);

            if ($ticket->status === 'closed')
                return errorResponse("Ticket đã đóng, không thể trả lời được!");

            if ($ticket->status === 'open')
                return errorResponse("Vui lòng đổi trạng thái sang resolving để tiếp nhận hỗ trợ!");

            $ticket->messages()->create([
                'sender_type' => get_class(auth('admin')->user()),
                'sender_id' => auth('admin')->id(),
                'message' => $credentials['message']
            ]);

            return successResponse("trả lời ticket thành công.");
        });
    }

    public function handleChangeStatus($credentials)
    {
        $ticket = $this->findById($credentials['ticketId']);

        // Nếu admin chọn "resolved"
        if ($credentials['status'] === 'resolved') {
            // Nếu chưa có admin xác nhận
            if (!$ticket->is_confirmed) {
                $ticket->update([
                    'is_confirmed' => true, // admin xác nhận
                ]);

                return successResponse("Đã gửi xác nhận 'resolved'. Chờ khách hàng phản hồi.");
            }
        }

        $ticket->update(['status' => $credentials['status']]);

        return successResponse("Thay đổi trạng thái thành công.");
    }
}
