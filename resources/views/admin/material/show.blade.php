@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            @php
                $items = ['name' => "nguyên vật liệu - $material->name"];
            @endphp
            <x-breadcrumb :items="[$items]" />
        </div>

        <div class="card">
            <div class="card-body">

                {{-- Nav Tabs --}}
                <div class="nav-tabs-wrapper" style="overflow-x: auto; white-space: nowrap;">
                    <ul class="nav nav-tabs mb-3" id="importTab" role="tablist" style="flex-wrap: nowrap;">
                        @foreach ($groupedByDate as $date => $details)
                            @php
                                $tabId = 'tab' . \Illuminate\Support\Str::slug($date);
                            @endphp
                            <li class="nav-item" role="presentation">
                                <button class="nav-link @if ($loop->first) active @endif"
                                    id="{{ $tabId }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $tabId }}"
                                    type="button" role="tab" aria-controls="{{ $tabId }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>



                {{-- Tab Content --}}
                <div class="tab-content" id="importTabContent">
                    @foreach ($groupedByDate as $date => $details)
                        @php
                            $tabId = 'tab' . \Illuminate\Support\Str::slug($date);
                            $groupedByCode = $details->groupBy(fn($item) => $item->import->import_code);
                            $stt = 1;
                        @endphp

                        <div class="tab-pane fade @if ($loop->first) show active @endif"
                            id="{{ $tabId }}" role="tabpanel" aria-labelledby="{{ $tabId }}-tab">

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover mt-3">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã nhập</th>
                                            <th>Loại</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá (USD)</th>
                                            <th>Đơn vị</th>
                                            <th>Tổng (USD)</th>
                                            <th>Nhà cung cấp</th>
                                            <th>Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groupedByCode as $code => $items)
                                            @foreach ($items as $i => $item)
                                                <tr>
                                                    <td>{{ $stt++ }}</td>

                                                    {{-- Gộp mã nhập --}}
                                                    @if ($i === 0)
                                                        <td rowspan="{{ $items->count() }}">{{ $code }}</td>
                                                    @endif

                                                    <td>{{ $item->type->name ?? 'Không có' }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ formatPrice($item->price) }}</td>
                                                    <td>{{ $item->unit }}</td>
                                                    <td>{{ formatPrice($item->price * $item->quantity) }}</td>
                                                    <td>{{ $item->supplier_name }}</td>

                                                    {{-- Gộp thời gian --}}
                                                    @if ($i === 0)
                                                        <td rowspan="{{ $items->count() }}">
                                                            {{ $item->created_at->format('d/m/Y H:i') }}</td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style>
        .nav-tabs .nav-link {
            font-weight: bold;
            color: #333;
        }

        .nav-tabs .nav-link.active {
            background-color: #0d6efd;
            color: white;
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .nav-tabs-wrapper {
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .nav-tabs {
        flex-wrap: nowrap !important;
    }

    .nav-tabs .nav-link {
        white-space: nowrap;
    }
    </style>
@endpush
