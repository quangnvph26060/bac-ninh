<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Coupon\CouponRequest;
use App\Services\CouponService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    use PaginateTrait;

    public function __construct(public CouponService $couponService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = $this->couponService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('start_date', fn($row) => $row->start_date->format('d-m-Y H:i'))
                    ->editColumn('end_date', fn($row) => $row->end_date->format('d-m-Y H:i')) // ✅ fix ở đây
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations']
            );
        }
        return view('admin.coupon.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Thêm mới mã giảm giá.';
        $coupon = null;
        return view('admin.coupon.save', compact('coupon', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request)
    {
        $payload = $request->validated();
        $response = $this->couponService->store($payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $title = 'Cập nhật thương hiệu';
        $coupon = $this->couponService->show($id);
        return view('admin.coupon.save', compact('coupon', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponRequest $request, string $id)
    {
        $payload = $request->validated();

        $response = $this->couponService->update($id, $payload);
        return handleResponse($response['message'], $response['success'], $response['code']);
    }

    // if (!empty($payload['cross_sell'])) {
    //     $payload['cross_sell'] = array_map('intval', explode(',', $payload['cross_sell']));
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
