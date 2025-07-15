<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\VoucherType;

class JournalEntryController extends Controller
{
    public function index()
    {
        $voucherTypes = VoucherType::query()->pluck('name', 'id')->toArray();
        $journalEntries = JournalEntry::query()->get();
        //  dd($journalEntry);
        if (request()->ajax()) {
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