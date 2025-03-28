<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Client;
use App\Models\ClientGroup;
use App\Services\ClientGroupService;
use App\Services\ClientService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ClientController extends Controller
{
    protected $clientService;
    protected $clientGroupService;
    public function __construct(ClientService $clientService, ClientGroupService $clientGroupService)
    {
        $this->clientService = $clientService;
        $this->clientGroupService = $clientGroupService;
    }
    public function index()
    {
        $title = 'Khách hàng';
        try {
            $clients = $this->clientService->getAllClient();
            return view('admin.client.index', compact('clients', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to fetch clients: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch clients'], 500);
        }
    }

    public function findClient(Request $request)
    {
        $title = 'Khách hàng';
        try {
            $client = $this->clientService->findClientByPhone($request->phone);

            // Convert single client to a paginator instance
            $clients = new LengthAwarePaginator(
                $client ? [$client] : [],
                $client ? 1 : 0,
                10,
                1,
                ['path' => Paginator::resolveCurrentPath()]
            );

            return view('admin.client.index', compact('clients', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to find client: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to find client'], 500);
        }
    }
    public function edit($id)
    {
        $title = 'Sửa thông tin khách hàng';
        try {
            $clientgroups = $this->clientGroupService->getAllClientGroup();
            $client =  $this->clientService->getClientByID($id);
            return view('admin.client.edit', compact('client', 'title', 'clientgroups'));
        } catch (Exception $e) {
            Log::error('Failed to find client profile');
        }
    }

    public function update($id, Request $request)
    {
        try {
            $client = $this->clientService->updateClient($id, $request->all());
            session()->flash('success', 'Cập nhật thông tin khách hàng thành công!');
            return redirect()->route('admin.client.index');
        } catch (\Exception $e) {
            Log::error('Failed to update client profile: ' . $e->getMessage());
            return ApiResponse::error('Failed to update client profile', 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->clientService->deleteClient($id);
            $clients = Client::orderByDesc('created_at')->paginate(10);
            $view = view('admin.client.table', compact('clients'))->render();
            return response()->json(['success' => true, 'message' => 'Xóa thành công!', 'table' => $view]);
        } catch (Exception $e) {
            Log::error('Failed to delete client: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Khách hàng không thể xóa.']);
        }
    }

    public function clientgroup()
    {
        try {
            $title = 'Nhóm khách hàng';
            $clientgroup = $this->clientGroupService->getAllClientGroup();
            // dd($clientgroup);
            return view('admin.client.group.index', compact('clientgroup', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to list clientgroup: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'không có loại khách hàng.']);
        }
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $clients = Client::all();

        $sheet->setCellValue('A1', 'Mã khách hàng');
        $sheet->setCellValue('B1', 'Tên khách hàng');
        $sheet->setCellValue('C1', 'Giới tính');
        $sheet->setCellValue('D1', 'Ngày sinh');
        $sheet->setCellValue('E1', 'Số điện thoại');
        $sheet->setCellValue('F1', 'Email');
        $sheet->setCellValue('G1', 'Mã bưu chính');
        $sheet->setCellValue('H1', 'Địa chỉ');

        $row = 2;
        foreach ($clients as $client) {
            $sheet->setCellValue('A' . $row, $client->id ?? '');
            $sheet->setCellValue('B' . $row, $client->name ?? '');
            $sheet->setCellValue('C' . $row, isset($client->gender) ? (($client->gender == 0) ? 'Nam' : 'Nữ') : '');
            $sheet->setCellValue('D' . $row, Carbon::parse($client->dob)->format('d/m/Y') ?? '');
            $sheet->setCellValue('E' . $row, $client->phone ?? '');
            $sheet->setCellValue('F' . $row, $client->email ?? '');
            $sheet->setCellValue('G' . $row, $client->zip_code ?? '');
            $sheet->setCellValue('H' . $row, $client->address ?? '');
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(50);

        $write = new Xlsx($spreadsheet);

        $fileName = 'Danh sách khách hàng.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $write->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}
