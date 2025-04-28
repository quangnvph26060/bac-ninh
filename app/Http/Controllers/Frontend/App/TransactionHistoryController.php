<?php

namespace App\Http\Controllers\Frontend\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionHistoryController extends Controller
{
    public function transactionHistory(Request $request)
    {
        return view('frontend.app.transaction-history.index');
    }
}
