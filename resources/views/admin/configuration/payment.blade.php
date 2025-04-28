@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'cấu hình thanh toán']]" />
        </div>

        <form action="" method="post" id="myForm">
            @method('PUT')

            <div class="row">
                <div class="gap-3 col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-body">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Người hướng thụ</th>
                                            <th>Số tài khoản</th>
                                            <th>Tên ngân hàng</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($configPayments as $key => $configPayment)
                                            <tr>
                                                <td style="width: 25%">
                                                    <input class="form-control" type="text"
                                                        name="accounts[{{ $key }}][enjoyer]"
                                                        value="{{ $configPayment->enjoyer }}">
                                                </td>
                                                <td style="width: 25%">
                                                    <input class="form-control" type="text"
                                                        name="accounts[{{ $key }}][account_number]"
                                                        value="{{ $configPayment->account_number }}">
                                                </td>
                                                <td>
                                                    <select class="form-control select-bank"
                                                        name="accounts[{{ $key }}][bank_id]">
                                                        @foreach ($banks as $id => $shortName)
                                                            <option value="{{ $id }}"
                                                                @selected($id == $configPayment->bank_id)>
                                                                {{ $shortName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td style="width: 5%">
                                                    <button class="btn btn-danger btn-sm btn-delete"><i
                                                            class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button type="button" class="btn btn-success btn-sm">+ Thêm tài
                                                    khoản</button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 gap-3 d-flex flex-column-reverse flex-md-column mb-md-0 mb-5">
                    @include('admin.components.button')
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/plugin/select2/select2.min.js') }}"></script>

    <script>
        var banks = @json($banks);

        function generateBankOptions(selectedBin = '') {
            let options = `<option value="" selected disabled>Chọn ngân hàng</option>`;
            Object.entries(banks).forEach(([id, name]) => {
                let selected = (id === selectedBin) ? 'selected' : '';
                options += `<option value="${id}" ${selected}>${name}</option>`;
            });
            return options;
        }

        initSelect2();
        updateBankOptions();

        function initSelect2() {
            $(".select-bank").select2({
                placeholder: "Chọn ngân hàng",
                allowClear: true,
                width: '100%',
            }).on('change', function() {
                updateBankOptions();
            });
        }

        function updateBankOptions() {
            $(".select-bank").each(function() {
                let selectedValue = $(this).val();

                $(".select-bank").not(this).each(function() {
                    $(this).find(`option[value="${selectedValue}"]`).prop('disabled', true);
                });
            });
        }

        $(".btn-success").click(function() {
            let index = $(".form-body tbody tr").length;
            console.log(index);

            let newRow = `
                <tr>
                    <td style="width: 25%"><input class="form-control" type="text" name="accounts[${index}][enjoyer]"></td>
                    <td style="width: 25%"><input class="form-control" type="text" name="accounts[${index}][account_number]"></td>
                    <td>
                        <select class="form-control select-bank" name="accounts[${index}][bank_id]">
                            ${generateBankOptions()}
                        </select>
                    </td>
                    <td style="width: 5%"><button class="btn btn-danger btn-sm btn-delete"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            $("tbody").append(newRow);
            initSelect2();
            updateBankOptions()
        });

        $(document).on("click", ".btn-delete", function() {
            if ($("tbody tr").length > 1) {
                $(this).closest("tr").remove();
                updateBankOptions();
            } else {
                alert("Phải có ít nhất một hàng!");
            }
        });

        $(document).ready(function() {
            submitForm('#myForm', function(response) {
                Notifications(response.message, "success");
            })
        })
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('backend/assets/css/select2.min.css') }}">
@endpush
