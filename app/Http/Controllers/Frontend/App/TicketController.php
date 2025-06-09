<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subject;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {

        $perPage = $request->input('per_page', 10);

        $userId = auth('web')->id();

        $query = Ticket::query()
            ->where('user_id', $userId)
            ->with(['user', 'order', 'subject'])
            ->when($request->status && $request->status !== 'all', function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->subject, function ($q) use ($request) {
                $q->where('subject_id', $request->subject);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where('order_code', 'like', '%' . $request->search . '%');
            })
            ->when($request->date_range, function ($q) use ($request) {
                [$start, $end] = explode(' - ', $request->date_range);
                $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
                $q->whereBetween('created_at', [$start, $end]);
            });

        $tickets = $query->orderBy('updated_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            $html = view('frontend.app.ticket._table', compact('tickets'))->render();
            return response()->json([
                'html' => $html,
            ]);
        }

        $statusCounts = Ticket::getStatusCountsByUser($userId);

        $totalCount = $statusCounts->sum();

        return view('frontend.app.ticket.index', compact('tickets', 'statusCounts', 'totalCount'));
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'order_id' => 'required|exists:orders,id',
            'content' => 'required|string'
        ], [], [
            'order_id' => 'order',
            'subject_id' => 'subject',
        ]);

        try {
            DB::beginTransaction();
            $credentials['user_id'] = auth('web')->id();
            $credentials['code'] = generateTicketCode();

            $ticket = Ticket::create($credentials);

            $ticket->messages()->create([
                'sender_type' => get_class(auth('web')->user()),
                'sender_id' => $credentials['user_id'],
                'message' => $credentials['content']
            ]);
            DB::commit();

            $statusCounts = Ticket::getStatusCountsByUser(auth('web')->id());
            $totalCount = $statusCounts->sum();
            $orderTickets = Ticket::where('order_id', $credentials['order_id'])
                ->with(['subject']) // tùy bạn muốn load gì
                ->orderBy('created_at', 'desc')
                ->get();

            return handleResponse("Ticket created successfully", true, 200, [
                'statusCounts' => $statusCounts,
                'totalCount' => $totalCount,
                'tickets' => $orderTickets,
            ], false);
        } catch (\Exception $e) {
            logger('TicketController(store): ' . $e->getMessage());
            DB::rollBack();
            return errorResponse("Ticket creation failed", true, 400);
        }
    }

    public function show($id)
    {
        $ticket = Ticket::with(['subject', 'order', 'messages.sender', 'user'])->findOrFail($id);

        $html = view('frontend.app.ticket._modal_detail', compact('ticket'))->render();

        return response()->json(['html' => $html]);
    }

    public function send(Request $request)
    {
        $credentials = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'message' => 'required|string'
        ], [], [
            'ticket_id' => 'order',
        ]);

        $ticket = Ticket::query()->find($credentials['ticket_id']);

        $ticket->messages()->create([
            'sender_type' => get_class(auth('web')->user()),
            'sender_id' => auth('web')->id(),
            'message' => $credentials['message']
        ]);

        return handleResponse("Ticket reply successful", true, 201, null, false);
    }

    public function close(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'reason' => 'required|string|max:1000',
        ]);

        $ticket = Ticket::find($request->ticket_id);
        $ticket->status = 'closed';
        $ticket->reason = $request->reason;
        $ticket->save();

        // Ghi lại message đóng
        $ticket->messages()->create([
            'sender_type' => get_class(auth('web')->user()),
            'sender_id' => auth('web')->id(),
            'message' => '<strong>Close ticket:</strong> ' . e($request->reason)
        ]);

        $statusCounts = Ticket::getStatusCountsByUser(auth('web')->id());
        $totalCount = $statusCounts->sum();
        $orderTickets = Ticket::where('order_id', $ticket->order_id)
            ->with(['subject']) // tùy bạn muốn load gì
            ->orderBy('created_at', 'desc')
            ->get();

        return handleResponse('Ticket closed successfully', true, 200, [
            'statusCounts' => $statusCounts,
            'totalCount' => $totalCount,
            'tickets' => $orderTickets,
        ], false);
    }

    public function rate(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'rating' => 'required|integer|min:1|max:5',
            'description' => 'nullable|string|max:1000',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);
        $ticket->rating = $request->rating;
        $ticket->feedback = $request->description;
        $ticket->status = 'resolved';
        $ticket->is_confirmed = ! $ticket->is_confirmed;
        $ticket->save();

        $statusCounts = Ticket::getStatusCountsByUser(auth('web')->id());
        $totalCount = $statusCounts->sum();

        return handleResponse('Saved successfully', true, 200, [
            'statusCounts' => $statusCounts,
            'totalCount' => $totalCount,
        ], false);
    }
}
