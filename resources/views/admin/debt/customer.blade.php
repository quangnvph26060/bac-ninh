@extends('admin.layout.index')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <x-breadcrumb :items="[['name' => 'công nợ khách hàng']]" />
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th rowspan="2" class="row-number">#</th>
                        <th rowspan="2">Khách hàng</th>
                        <th rowspan="2">Số điện thoại</th>
                        <th colspan="2" class="header-main">Số dư đầu kì</th>
                        <th colspan="2" class="header-main">Phát sinh trong kì</th>
                        <th colspan="2" class="header-main">Số dư cuối kì</th>
                    </tr>
                    <tr>
                        <th class="header-sub">Nợ [Phải thu]</th>
                        <th class="header-sub">Có [Phải trả]</th>
                        <th class="header-sub">Ghi nợ</th>
                        <th class="header-sub">Ghi có</th>
                        <th class="header-sub">Nợ [Phải thu] = 4 + 6 - 5 - 7</th>
                        <th class="header-sub">Có [Phải trả] = 5 + 7 - 4 - 6</th>
                    </tr>
                    <tr>
                        <th class="header-sub">[1]</th>
                        <th class="header-sub">[2]</th>
                        <th class="header-sub">[3]</th>
                        <th class="header-sub">[4]</th>
                        <th class="header-sub">[5]</th>
                        <th class="header-sub">[6]</th>
                        <th class="header-sub">[7]</th>
                        <th class="header-sub">[8]</th>
                        <th class="header-sub">[9]</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
