<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BulkActionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CheckInventoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\DailyReportController;
use App\Http\Controllers\Admin\DebtClientController;
use App\Http\Controllers\Admin\DebtNccController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\importCouponController;
use App\Http\Controllers\Admin\ImportProductController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportdebtController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Client\SignUpController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Frontend\App\BillController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('admin.auth')->group(function () {

        // Dashboard Router
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

        // Logout Router
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // Action Router
        Route::post('handle-bulk-action', [BulkActionController::class, 'handleBulkAction'])->name('handle.bulk.action');

        // Product Router
        Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('import', 'import')->name('import');
            Route::get('export', 'export')->name('export');
            Route::get('download-product-template', 'downloadTemplate')->name('download.product.template');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
            Route::get('search-products', 'search')->name('search.products');
            Route::get('selected-attributes/{id}',  'getValueByAttributeId')->name('selected.attributes');
        });

        // Attribute Router
        Route::prefix('attributes')->controller(AttributeController::class)->name('attributes.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
        });

        // Category Router
        Route::prefix('categories')->controller(CategoryController::class)->name('categories.')->group(function () {
            Route::get('/',   'index')->name('index');
            Route::get('create',  'create')->name('create');
            Route::post('create',  'store')->name('store');
            Route::get('edit/{id}',  'edit')->name('edit');
            Route::put('edit/{id}',  'update')->name('update');
        });

        // Collection Router
        Route::prefix('collections')->controller(CollectionController::class)->name('collections.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
        });

        // Brand Router
        Route::prefix('brands')->controller(BrandController::class)->name('brands.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
        });

        // Supplier Router
        Route::prefix('suppliers')->controller(SupplierController::class)->name('suppliers.')->group(function () {
            Route::get("/", 'index')->name('index');
            Route::get("create", 'create')->name('create');
            Route::get("edit/{id}", 'edit')->name('edit');
            Route::put("edit/{id}", 'update')->name('update');
        });

        Route::prefix('coupons')->name('coupons.')->controller(CouponController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
        });

        Route::group(['prefix' => 'employees', 'controller' => EmployeeController::class, 'as' => 'employees.'], function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
        });

        Route::group(['prefix' => 'roles', 'controller' => RoleController::class, 'as' => 'roles.'], function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
        });

        Route::group(['prefix' => 'permissions', 'controller' => PermissionController::class, 'as' => 'permissions.'], function () {
            Route::get('/', 'index')->name('index');
            Route::post('save/{id?}', 'save')->name('save');
            Route::delete('destroy/{id}', 'destroy')->name('destroy');
        });

        Route::group(
            [
                'prefix' => 'configurations',
                'controller' => ConfigurationController::class,
                'as' => 'configurations.'
            ],
            function () {
                Route::get('/', 'configuration')->name('index');
                Route::put('/', 'updateConfiguration')->name('update.configuration');
                Route::get('payment', 'payment')->name('payment');
                Route::put('payment', 'updateConfigPayment');
            }
        );
    });

    // Auth Router
    Route::middleware('admin.guest')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('login', 'login')->name('login');
            Route::post('login', 'authenticate')->name('login.authenticate');
            Route::get('verify-otp', 'showVerifyOtp')->name('verify-otp');
            Route::post('verify-otp', 'verifyOtp')->name('verify_otp_confirm');
        });
    });

    // Transaction Router
    Route::prefix('transaction')->name('transaction.')->group(function () {
        Route::get('', [TransactionController::class, 'index'])->name('index');
        Route::get('search', [TransactionController::class, 'search'])->name('search');
        Route::get('payment', [TransactionController::class, 'payment'])->name('payment');
        Route::post('store', [TransactionController::class, 'store'])->name('store');
        Route::get('export-pdf/{id}', [TransactionController::class, 'exportPDF'])->name('export_pdf');
        Route::get('generateQR', [TransactionController::class, 'generateQrCode'])->name('generate');
    });

    // Company Router
    Route::prefix('company')->name('company.')->group(function () {
        Route::get("/", [CompanyController::class, 'index'])->name('index');
        Route::get('findByName', [CompanyController::class, 'findByName'])->name('findByName');
        Route::get('/add', [CompanyController::class, 'add'])->name('add');
        Route::post('/store', [CompanyController::class, 'store'])->name('store');
        Route::get('detail/{id}', [CompanyController::class, 'edit'])->name('detail');
        Route::post('update/{id}', [CompanyController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [CompanyController::class, 'delete'])->name('delete');
        Route::get('filter', [CompanyController::class, 'companyFilter'])->name('filter');
    });

    Route::prefix('profit')->name('profit.')->group(function () {
        Route::get('', [ReportController::class, 'profitIndex'])->name('index');
        Route::post('/profit-report', [ReportController::class, 'getProfitReportByFilterNew'])->name('getProfitReportByFilter');
        Route::post('/profit-report-pdf', [ReportController::class, 'getProfitReportByFilterPDF'])->name('getProfitReportByFilterPDF');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('', [ReportController::class, 'index'])->name('index');
        Route::post('report', [ReportController::class, 'getReportByStorage'])->name('getReportByStorage');
        Route::get('exportPdf', [ReportController::class, 'exportPdf'])->name('exportPdf');
    });

    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [ClientController::class, 'edit'])->name('detail');
        Route::put('/update/{id}', [ClientController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ClientController::class, 'delete'])->name('delete');
        Route::get('/filter', [ClientController::class, 'findClient'])->name('filter');
        Route::get('/clientgroup', [ClientController::class, 'clientgroup'])->name('clientgroup.index');
        Route::get('/export', [ClientController::class, 'export'])->name('export');
    });

    Route::prefix('order')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [OrderController::class, 'detail'])->name('detail');
        // Route::get('/find/phone', [OrderController::class, 'getOrderbyPhone'])->name('findByPhone');
        Route::get('/admin/order/filter', [OrderController::class, 'filterOrder'])->name('filter');
    });

    Route::prefix('checkInventory')->name('check.')->group(function () {
        Route::get('/', [CheckInventoryController::class, 'index'])->name('index');
        Route::get('/filter', [CheckInventoryController::class, 'filterCheck'])->name('filter');
        Route::get('/detail/{id}', [CheckInventoryController::class, 'detail'])->name('detail');
    });
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [SupportController::class, 'contact'])->name('lienhe');
        Route::post('/', [SupportController::class, 'feedback'])->name('feedback');
    });

    Route::prefix('importproduct')->name('importproduct.')->group(function () {
        Route::get('/', [ImportProductController::class, 'index'])->name('index');
        Route::get('/add', [ImportProductController::class, 'add'])->name('add');
        Route::get('/import', [ImportProductController::class, 'listImport'])->name('import');
        Route::post('/import/add', [ImportProductController::class, 'importadd'])->name('import.add');
        Route::post('/import/update', [ImportProductController::class, 'importupdate'])->name('import.update');
        Route::post('/import/update/price', [ImportProductController::class, 'importupdateprice'])->name('import.update.price');
        Route::get('/import/delete', [ImportProductController::class, 'importdelete'])->name('import.delete');
        Route::post('/import/addCategory', [ImportProductController::class, 'addCategory'])->name('import.addCategory');
        // tạo phiếu
        Route::post('/importCoupon', [importCouponController::class, 'add'])->name('importCoupon.add');
        Route::get('/detail/{id}', [ImportProductController::class, 'importdetail'])->name('importCoupon.detail');
    });

    Route::prefix('debts')->name('debts.')->group(function () {
        Route::get('/client', [DebtClientController::class, 'index'])->name('client');
        Route::get('/client/detail/{id}', [DebtClientController::class, 'detail'])->name('client.detail');
        Route::get('/supplier', [DebtNccController::class, 'index'])->name('supplier');
        Route::get('/supplier/detail/{id}', [DebtNccController::class, 'detail'])->name('supplier.detail');
    });

    Route::prefix('quanlythuchi')->name('quanlythuchi.')->group(function () {
        Route::prefix('receipts')->name('receipts.')->group(function () { // phiếu thu
            Route::get('/', [ReceiptController::class, 'index'])->name('index');
            Route::get('/detail/{id}', [ReceiptController::class, 'detail'])->name('detail');
            Route::get('/add', [ReceiptController::class, 'add'])->name('add');
            Route::post('/add', [ReceiptController::class, 'addSubmit'])->name('addSubmit');
            Route::post('/debt', [ReceiptController::class, 'debt'])->name('debt');
        });
        Route::prefix('expense')->name('expense.')->group(function () { // phiếu chi
            Route::get('/', [ExpenseController::class, 'index'])->name('index');
            Route::get('/detail/{id}', [ExpenseController::class, 'detail'])->name('detail');
            Route::get('/add', [ExpenseController::class, 'add'])->name('add');
            Route::post('/add', [ExpenseController::class, 'addSubmit'])->name('addSubmit');
            Route::post('/debt', [ExpenseController::class, 'debt'])->name('debt');
        });
    });

    Route::prefix('storage')->name('storage.')->group(function () {
        Route::get('', [StorageController::class, 'index'])->name('index');
        Route::get('detail/{id}', [StorageController::class, 'edit'])->name('detail');
        Route::post('update/{id}', [StorageController::class, 'update'])->name('update');
        Route::get('add', [StorageController::class, 'add'])->name('add');
        Route::post('create', [StorageController::class, 'create'])->name('create');
        Route::get('findByName', [StorageController::class, 'findStorageByName'])->name('findByName');
        Route::delete('delete/{id}', [StorageController::class, 'delete'])->name('delete');
        Route::get('/products/{id}', [StorageController::class, 'detail'])->name('products');
    });

    Route::prefix('report')->name('report.')->group(function () {
        Route::prefix('debt')->name('debt.')->group(function () {
            Route::get('/', [ReportdebtController::class, 'index'])->name('index');
            Route::get('/print', [ReportdebtController::class, 'print'])->name('print');
        });
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('', [DailyReportController::class, 'getDailyOrder'])->name('getDailyOrder');
            Route::get('get-daily-order-data', [DailyReportController::class, 'getDailyOrderData'])->name('getDailyOrderData');
        });
        Route::prefix('imports')->name('imports.')->group(function () {
            Route::get('', [DailyReportController::class, 'getDailyImport'])->name('getDailyImport');
            Route::get('get-daily-import-data', [DailyReportController::class, 'getDailyImportData'])->name('getDailyImportData');
        });
    });
});

