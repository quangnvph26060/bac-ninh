<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function customer()
    {
        return view('admin.debt.customer');
    }
}
