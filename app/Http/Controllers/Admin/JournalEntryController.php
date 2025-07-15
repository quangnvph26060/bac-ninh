<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class JournalEntryController extends Controller
{
    public function index()
    {
        return view('admin.journal-entry.index');
    }
}