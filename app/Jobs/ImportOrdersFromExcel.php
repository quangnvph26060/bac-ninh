<?php

namespace App\Jobs;

use App\Imports\OrderImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ImportOrdersFromExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected int $userId;
    protected string $jobId;

    public function __construct(string $filePath, int $userId, string $jobId)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
        $this->jobId = $jobId;
    }

    public function handle(): void
    {
        // Gọi import
        Excel::import(new OrderImport($this->userId, $this->jobId), $this->filePath);
    }
}
