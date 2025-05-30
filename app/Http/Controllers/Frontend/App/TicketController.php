<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Subject;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->status;
        $payment_status = $request->payment_status;
        $search = $request->search;
        $dateRange = $request->date_range;
        $perPage = $request->input('per_page', 10);

        $query = Ticket::query()->where('user_id', auth('web')->id())->with(['user', 'order', 'subject']);

        if ($status && $status !== "all") {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where('order_code', 'like', '%' . $search . '%');
        }

        if ($dateRange) {
            [$start, $end] = explode(' - ', $dateRange);
            $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
            $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $tickets = $query->orderBy('updated_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            $html = view('frontend.app.ticket._table', compact('tickets'))->render();
            return response()->json([
                'html' => $html,
            ]);
        }

        $statusCounts = Ticket::where('user_id', auth('web')->id())
            ->selectRaw("status, COUNT(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalCount = $statusCounts->sum();

        $orders = Order::query()->where('user_id', auth('web')->id())->select(['id', 'order_code', 'order_name'])->get();

        $subjects = Subject::query()->where('status', 1)->pluck('title', 'id');

        return view('frontend.app.ticket.index', compact('tickets', 'subjects', 'statusCounts', 'totalCount', 'orders'));
    }
}
