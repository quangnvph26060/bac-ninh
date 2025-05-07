<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    use PaginateTrait;

    public function __construct(public ActivityService $activityService) {}

    public function history($id = null)
    {

        if (!empty($id)) {
            $data = $this->activityService->show($id);

            $changes = is_string($data->changes) ? json_decode($data->changes, true) : $data->changes;

            return successResponse("lấy dữ liệu thành công.", $changes, 200, true);
        }

        if (request()->ajax()) {
            $query = $this->activityService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('changes', fn($row) => '<button class="btn btn-primary btn-sm btn-view-changes" data-id="' . $row->id . '">👁️</button>')
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d/m/Y H:i:s')),
                ['changes']
            );
        }

        return view('admin.activity-log.index');
    }
}
