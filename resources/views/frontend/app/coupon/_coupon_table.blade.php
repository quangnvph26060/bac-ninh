<table class="table table-striped custom-table">
    <thead>
        <tr class="align-middle">
            <th scope="col" style="width: 5%">ID</th>
            <th scope="col" style="width: 10%">Mã</th>
            <th scope="col" style="width: 40%">Tiêu đề</th>
            <th scope="col" class="text-center">Loại</th>
            <th scope="col">Trạng thái</th>
            <th scope="col" class="text-center">Còn hạn trong</th>
            <th scope="col" style="width: 13%">Số lượt sử dụng còn lại</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($coupons as $coupon)
            <tr class="align-middle">
                <td>{{ $coupon->id }}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <p class="mb-0 coupon-code">{{ $coupon->code }}</p>
                        <i class="bi bi-clipboard copy-btn" role="button" title="Sao chép"
                            data-code="{{ $coupon->code }}"></i>
                    </div>
                </td>

                <td>
                    <p>{{ $coupon->title }}</p>
                </td>
                <td>
                    <p class="mb-0 text-center border rounded-pill py-1">{{ ucfirst($coupon->type) }}</p>
                </td>
                <td>
                    @include('frontend.components.switch-status', ['status' => $coupon->status])
                </td>

                <td class="text-center">
                    {!! now()->diffInDays($coupon->end_date, false) > 0
                        ? '<span class="text-success">' . now()->diffInDays($coupon->end_date) . " ngày</span>"
                        : '<span class="text-danger">Đã hết hạn</span>' !!}
                </td>

                <td>
                    <p class="mb-0 text-center border rounded-pill py-1 align-middle">
                        {{ $coupon->users->count() }} / <span class="align-middle">
                            {!! $coupon->usage_limit ?? '<i class="bi bi-infinity"></i>' !!}
                        </span>
                    </p>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="fw-bold text-muted">Không có mã giảm giá nào được tìm thấy</div>
                </td>
            </tr>
        @endforelse
    </tbody>

</table>

{{ $coupons->links('vendor.pagination.custom') }}
