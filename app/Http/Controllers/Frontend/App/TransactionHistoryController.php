<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use App\Models\TransactionHistory;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    public function transactionHistory(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $dateRange = $request->date_range;

        $wallet = Wallet::query()->where('user_id', auth()->id())->firstOrFail();

        $walletTransactions = WalletTransaction::query()->where('wallet_id', $wallet->id)
            ->when(!empty($search), fn($q) => $q->where('code', 'like', '%' . $search . '%'))
            ->when(!empty($dateRange), function ($q) use ($dateRange) {
                [$start, $end] = explode(' - ', $dateRange);
                $start = Carbon::createFromFormat('d/m/Y', trim($start))->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', trim($end))->endOfDay();
                $q->whereBetween('created_at', [$start, $end]);
            })
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            $html = view('frontend.app.transaction-history._table', compact('walletTransactions'))->render();
            return response()->json([
                'html' =>  $html
            ]);
        }

        return view('frontend.app.transaction-history.index');
    }
}
