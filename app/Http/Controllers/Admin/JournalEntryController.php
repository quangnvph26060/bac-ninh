<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $voucherTypes = VoucherType::query()->pluck('name', 'id')->toArray();
        $query = JournalEntry::query()->latest();

        // Lọc
        if (request()->ajax()) {
            if ($request->filled('date_range')) {

                // Tách ngày bắt đầu và kết thúc
                $dates = explode(
                    ' - ',
                    $request->date_range
                );

                try {
                    $start = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $end = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

                    $query->whereBetween('transaction_date', [$start, $end]);
                } catch (\Exception $e) {

                }
            }

            if ($request->filled('amount')) {
                $query->where('amount', $request->amount);
            }

            $journalEntries = $query->get();


            return response()->json([
                'success' => true,
                'html' => view('admin.journal-entry._table', compact('journalEntries'))->render()
            ]);
        }

        return view(
            'admin.journal-entry.index',
            compact(
                'voucherTypes'
            )
        );
    }
}