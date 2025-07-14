<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use App\Traits\PaginateTrait;
use Illuminate\Http\Request;


class CustomerController extends Controller
{
    use PaginateTrait;
    public function __construct(public CustomerService $customerService) {}

    public function index()
    {
        $this->authorize('view', User::class);

        if (request()->ajax()) {
            $query = $this->customerService->pagination();

            return $this->processDataTable(
                $query,
                fn($dataTable) =>
                $dataTable
                    ->editColumn('birthday', fn($row) => $row->birthday?->format('d-m-Y'))
                    ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y'))
                    ->addColumn('operations', fn($row) => view('admin.components.operation', compact('row'))),
                ['operations']
            );
        }
        return view('admin.customer.index');
    }

    public function create()
    {
        $title = 'Tạo mới khách hàng';
        return view('admin.customer.save', compact('title'));
    }

    public function store(Request $request)
    {
        $credentials = $this->validateRequest($request);

        return transaction(function () use ($credentials) {
            $credentials['code'] ??= generateCode('customers', 'KH');
            Customer::create($credentials);

            sessionFlash('success', 'Tạo mới khách hàng thành công');

            return successResponse(message: "Tạo mới khách hàng thành công.", isResponse: true);
        });
    }

    public function update(Request $request, Customer $customer)
    {
        $credentials = $this->validateRequest($request, $customer);

        return transaction(function () use ($credentials, $customer) {
            $credentials['code'] ??= $customer->code ?? generateCode('customers', 'KH');
            $customer->update($credentials);

            sessionFlash('success', 'Cập nhật khách hàng thành công');

            return successResponse(message: "Cập nhật khách hàng thành công.", isResponse: true);
        });
    }

    private function validateRequest(Request $request, ?Customer $customer = null)
    {
        $customerId = $customer?->id ?? 'NULL';

        return $request->validate([
            'name' => 'required|max:255',
            'company_name' => 'nullable|max:255',
            'code' => 'nullable|max:50',
            'company_tax_code' => 'nullable|max:50',
            'phone' => [
                'required',
                'regex:/^0\d{9,10}$/',
                'unique:customers,phone,' . $customerId,
            ],
            'company_address' => 'nullable|max:255',
            'email' => 'nullable|email|unique:customers,email,' . $customerId,
            'citizen_id' => 'nullable|numeric|digits_between:9,12',
            'birthday' => [
                'nullable',
                'date_format:Y-m-d',
                'before:' . now()->subYears(10)->format('Y-m-d'),
            ],
            'customer_type' => 'required|in:retail,wholesale,agent',
            'address' => 'nullable|max:255',
            'gender' => 'nullable|in:male,female,other',
            'note' => 'nullable|max:255'
        ], __('request.messages'), [
            'name' => 'tên khách hàng',
            'company_name' => 'tên công ty',
            'code' => 'mã khách hàng',
            'company_tax_code' => 'mã số thuế công ty',
            'phone' => 'số điện thoại',
            'company_address' => 'địa chỉ công ty',
            'email' => 'email',
            'citizen_id' => 'số CCCD/CMND',
            'birthday' => 'ngày sinh',
            'customer_type' => 'loại khách hàng',
            'address' => 'địa chỉ',
            'gender' => 'giới tính',
            'note' => 'ghi chú',
        ]);
    }


    public function edit(string $id)
    {
        $customer = Customer::query()->findOrFail($id);

        // dd($customer);

        $title = "Cập nhật khách hàng - $customer->name";

        return view('admin.customer.save', compact('title', 'customer'));
    }

    public function show($id)
    {
        $this->authorize('viewAny', User::class);

        $user = $this->customerService->getUserById($id);
        $total = $user->orders->sum(fn($order) => $order->total);
        return view('admin.customer.show', compact('user', 'total'));
    }
}
