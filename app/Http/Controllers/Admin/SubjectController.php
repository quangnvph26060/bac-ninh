<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\SubjectRequest;
use App\Services\SubjectService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use PaginateTrait;

    public function __construct(public SubjectService $subjectService) {}
    public function index()
    {
        // $this->authorize('view', Brand::class);

        if (request()->ajax()) {
            $query = $this->subjectService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                    ->editColumn(
                        'operations',
                        fn($row) =>
                        "
                            <button data-record='$row' data-id='$row->id'
                                class='btn btn-primary btn-sm table-actions btn-operation-edit'>
                                <i class='ti ti-edit'></i>
                            </button>
                            <button data-id='$row->id'
                                class='btn btn-danger btn-sm table-actions btn-operation-destroy'>
                                <i class='ti ti-trash'></i>
                            </button>
                        "
                    ),
                ['operations']
            );
        }
        return view('admin.subject.index');
    }

    public function store(SubjectRequest $request)
    {
        // $this->authorize('create', Brand::class);

        $credentials = $request->validated();
        $response = $this->subjectService->store($credentials);
        return handleResponse($response['message'], $response['success'], $response['code'], '', false);
    }

    public function update(SubjectRequest $request)
    {
        $credentials = $request->validated();
        $credentials['id'] = $request->id;
        $response = $this->subjectService->update($credentials);
        return handleResponse($response['message'], $response['success'], $response['code'], '', false);
    }
}
