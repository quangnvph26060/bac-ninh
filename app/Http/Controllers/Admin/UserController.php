<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Traits\PaginateTrait;

class UserController extends Controller
{
    use PaginateTrait;
    public function __construct(public UserService $userService) {}

    public function index()
    {
        $this->authorize('view', User::class);

        if (request()->ajax()) {
            $query = $this->userService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('img_url', fn($row) => showImage($row->img_url))
                    ->editColumn('day_of_birth', fn($row) => $row->day_of_birth?->format('d-m-Y'))
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
            );
        }
        return view('admin.user.index');
    }

    public function show($id)
    {
        $this->authorize('viewAny', User::class);

        $user = $this->userService->getUserById($id);
        $total = $user->orders->sum(fn($order) => $order->total);
        return view('admin.user.show', compact('user', 'total'));
    }
}
