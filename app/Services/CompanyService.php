<?php

namespace App\Services;

use App\Models\Company;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyService extends BaseService
{
    protected $company;
    public function __construct(Company $company)
    {
        parent::__construct($company);
    }

    public function getAllCompany(): LengthAwarePaginator
    {
        try {
            return $this->company->query()->latest()->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to fetch companies: ' . $e->getMessage());
            throw new Exception('Failed to fetch companies');
        }
    }

    public function getCompanyAll()
    {
        return $this->pluck(['id', 'name'], [], [], ['name', 'asc'], ['products']);
    }

    public function findCompanyById($id)
    {
        try {
            return $this->company->find($id);
        } catch (Exception $e) {
            Log::error('Failed to find company: ' . $e->getMessage());
            throw new Exception('Failed to find company');
        }
    }
    public function addCompany(array $data)
    {
        try {
            $company = $this->company->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'tax_number' => $data['tax_number'],
                'bank_account' => $data['bank_account'],
                'bank_id' => $data['bank_id'],
                'note' => $data['note'],
                'city_id' => $data['city_id'],
            ]);
            DB::commit();
            return $company;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add company: ' . $e->getMessage());
            throw new Exception('Failed to add company');
        }
    }

    public function updateCompany(array $data, $id)
    {
        DB::beginTransaction();
        try {
            $company = $this->company->find($id);
            if (!$company) {
                throw new \Exception('Company not found');
            }

            $company->update([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'tax_number' => $data['tax_number'],
                'bank_account' => $data['bank_account'],
                'bank_id' => $data['bank_id'],
                'note' => $data['note'],
                'city_id' => $data['city_id'],
            ]);

            DB::commit();
            return $company;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update company: ' . $e->getMessage());
            throw new Exception('Failed to update company');
        }
    }

    public function deleteCompany($id)
    {
        DB::beginTransaction();
        try {
            $company = $this->company->find($id);
            $company->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete company: ' . $e->getMessage());
            throw new Exception('Failed to delete company');
        }
    }
}
