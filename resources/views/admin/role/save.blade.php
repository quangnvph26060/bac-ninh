@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[
                ['name' => 'vai trò', 'url' => route('admin.roles.index')],
                ['name' => isset($role) ? $title . ' - ' . $role->name : $title],
            ]" />
        </div>

        <form action="" method="post" id="myForm">

            @isset($role)
                @method('PUT')
            @endisset

            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <input type="text" placeholder="Tên vai trò" id="name" name="name" class="form-control w-50"
                        value="{{ $role->name ?? '' }}">
                    <button class="btn btn-primary btn-sm ms-3">Lưu</button>
                </div>
            </div>

            @foreach ($permissions as $groupName => $permission)
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                        <div>
                            <strong>{{ $groupName }}</strong>
                            <span class="badge bg-light text-dark ms-2">{{ count($permission) }} quyền</span>
                        </div>
                        <div>
                            <input type="checkbox" class="form-check-input select-all cursor"
                                id="selectAll-{{ \Str::slug($groupName) }}">
                            <label for="selectAll-{{ \Str::slug($groupName) }}"
                                class="form-check-label ms-1 text-white cursor">Chọn tất cả</label>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-3">
                        @foreach ($permission as $item)
                            <div class="form-check">
                                <input class="form-check-input cursor" type="checkbox" name="permissions[]"
                                    id="{{ \Str::slug($item->name) }}" value="{{ $item->name }}"
                                    @checked(in_array($item->name, isset($role) ? $role->permissions->pluck('name')->toArray() : []))>
                                <label class="form-check-label mb-0 cursor"
                                    for="{{ \Str::slug($item->name) }}">{{ $item->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </form>

    </div>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {
            // Khi nhấn checkbox "Chọn tất cả"
            $('.card.mb-3').each(function() {
                const card = $(this);


                const allChecked = card.find('input[type="checkbox"]:not([id^="selectAll"])').length > 0 &&
                    card.find('input[type="checkbox"]:not([id^="selectAll"])').length ===
                    card.find('input[type="checkbox"]:not([id^="selectAll"]):checked').length;

                card.find('.select-all').prop('checked', allChecked);

                // Sự kiện khi nhấn "Chọn tất cả"
                card.find('.select-all').on('change', function() {
                    const isChecked = $(this).is(':checked');
                    card.find('input[type="checkbox"]:not([id^="selectAll"])').prop('checked',
                        isChecked);
                });

                // Sự kiện khi checkbox con thay đổi
                card.find('input[type="checkbox"]:not([id^="selectAll"])').on('change', function() {
                    const allChecked = card.find('input[type="checkbox"]:not([id^="selectAll"])')
                        .length ===
                        card.find('input[type="checkbox"]:not([id^="selectAll"]):checked').length;
                    card.find('.select-all').prop('checked', allChecked);
                });
            });

            submitForm('#myForm', function(response) {
                window.location.href = "{{ route('admin.roles.index') }}"
            })
        });
    </script>
@endpush
