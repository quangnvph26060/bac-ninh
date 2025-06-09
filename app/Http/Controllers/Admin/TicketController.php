<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TicketService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use PaginateTrait;

    public function __construct(public TicketService $ticketService)
    {
    }
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->ticketService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
            );
        }
        return view('admin.ticket.index');
    }

    public function reply(string $id)
    {
        $ticket = $this->ticketService->show($id);
        return view('admin.ticket.reply', compact('ticket'));
    }

    public function sendMessage(Request $request)
    {
        $credentials = $request->validate(
            [
                'ticket_id' => 'required|exists:tickets,id',
                'message' => 'required|string'
            ],
            __('request.messages'),
            [
                'ticket_id' => 'chủ thể',
                'message' => 'tin nhắn',
            ]
        );

        $response = $this->ticketService->send($credentials);

        return handleResponse($response['message'], $response['success'], $response['code'], null, false);
    }

    public function updateStatus(Request $request)
    {
        $credentials = $request->validate(
            [
                'ticketId' => 'required|exists:tickets,id',
                'status' => 'required|in:open,resolving,resolved,closed'
            ],
            __('request.messages'),
            [
                'ticketId' => 'chủ thể',
                'status' => 'trạng thái',
            ]
        );

        $response = $this->ticketService->handleChangeStatus($credentials);

        return handleResponse($response['message'], $response['success'], $response['code'], null, false);
    }

}
