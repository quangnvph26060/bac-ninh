<?php
// phpinfo();

use App\Exports\MaterialsDataSheet;
use App\Exports\MaterialsTemplateExport;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BulkActionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\CollectionController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\MaterialImportController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BomsController;
use App\Http\Controllers\Admin\CashBookController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\MaterialRequestController;
use App\Http\Controllers\Admin\PasswordChangeRequestController;
use App\Http\Controllers\Admin\ReceiptController;
use App\Http\Controllers\Admin\ReportdebtController;
use App\Http\Controllers\Admin\StorageController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\SupplierDebtController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\TransferHistoryController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('admin.auth')->group(function () {
        // Dashboard Router
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

        // Change avatar
        Route::post('avatar', [AdminController::class, 'updateAvatar'])->name('updateAvatar');

        // Logout Router
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('activity-log/{id?}', [ActivityController::class, 'history'])->name('activity.log.history');

        // Action Router
        Route::post('handle-bulk-action', [BulkActionController::class, 'handleBulkAction'])->name('handle.bulk.action');

        Route::prefix('transfer-histories')->controller(TransferHistoryController::class)->name('transfer.histories.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('reject', 'reject')->name('reject');
            Route::post('confirm', 'confirm')->name('confirm');
        });

        Route::prefix('customers')->controller(CustomerController::class)->name('customers.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('show/{id}', 'show')->name('show');
            // Route::post('create', 'store')->name('store');
            // Route::get('edit/{id}', 'edit')->name('edit');
            // Route::put('edit/{id}', 'update')->name('update');
        });

        Route::prefix('subjects')->controller(SubjectController::class)->name('subjects.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store');
            Route::put('/', 'update');
        });

        Route::prefix('chats')->controller(ChatController::class)->name('chats.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('load-more-users', 'loadMoreUsers')->name('loadMoreUsers');
            Route::post('send-message', 'sendMessage')->name('sendMessage');
            Route::get('messages/{userId}', 'getMessages')->name('messages');
        });

        Route::group(['prefix' => 'cashbook', 'controller' => CashBookController::class, 'as' => 'cashbook.'], function () {
            Route::get('/', 'index')->name('index');
            Route::get('list', 'list')->name('list');
            Route::post('voucher-types', 'voucherType');
            Route::get('save/{id?}', 'save')->name('save');
            Route::post('store', 'store');
            Route::put('update/{id}', 'update');
            Route::delete('destroy', 'destroy');
            Route::post('print-multiple', 'printMultiple');
        });

        Route::prefix('orders')->controller(OrderController::class)->name('orders.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
            Route::post('items', 'getItemByCode')->name('get.item.by.code');
            Route::post('cancel', 'cancelOrder')->name('cancel');
            Route::get('download-barcode/{barcode}', 'download')->name('barcode.download');
            Route::get('invoice/preview/{id}', 'printInvoice')->name('invoice.print');
            Route::post('update-status/{id}', 'updateStatus')->name('update.status');
            Route::get('barcode-scanner', 'barcodeScanner')->name('barcode.scanner');
            Route::post('get-by-barcode', 'getByBarcode')->name('get.by.barcode');
            Route::post('change-tracking', 'changeTracking')->name('change-tracking');
            Route::view('view-barcode-pdf', 'admin.pdf.barcode');
        });

        Route::group(['prefix' => 'accounting-accounts', 'controller' => AccountController::class, 'as' => 'accounting-accounts.'], function () {
            Route::get('/', 'index')->name('index');
            Route::get('list', 'list')->name('list');
            Route::get('search', 'search')->name('search');
            Route::post('/', 'store')->name('store');
            Route::put('/', 'update')->name('update');
            Route::delete('delete-multiple', 'destroy')->name('destroy');
            Route::get('export', 'export')->name('export');
        });

        Route::group(['prefix' => 'tickets', 'controller' => TicketController::class, 'as' => 'tickets.'], function () {
            Route::get('/', 'index')->name('index');
            Route::get('reply/{id}', 'reply')->name('reply');
            Route::post('send-message', 'sendMessage');
            Route::patch('update-status', 'updateStatus');
        });

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
            Route::get('selected-attributes/{id}', 'getValueByAttributeId')->name('selected.attributes');
        });

        Route::prefix('materials')->controller(MaterialController::class)->name('materials.')->group(function () {
            // Temp mẫu
            Route::get('download-template', function () {
                return Excel::download(new MaterialsTemplateExport, 'materials_template_' . date('d-m-Y') . '.xlsx');
            })->name('template');


            Route::post('import', [MaterialController::class, 'import'])->name('import');

            Route::get('export', function () {
                return Excel::download(new MaterialsDataSheet, 'danh_sach_nguyen_vat_lieu_' . date('d-m-Y') . '.xlsx');
            })->name('export');

            Route::get('/', 'index')->name('index');
            Route::get('search', 'search')->name('search');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
            Route::get('select2', 'select2');
            Route::get('/{id}', 'show')->name('show');
        });

        Route::group(
            [
                'prefix' => 'material-requests',
                'controller' => MaterialRequestController::class,
                'as' => 'material-requests.'
            ],
            function () {
                Route::get('/', 'index')->name('index');
                Route::get('create', 'create')->name('create');
                Route::post('create', 'store');
                Route::get('{id}/edit', 'edit');
                Route::put('{id}/edit', 'update');

                Route::post('{id}/approve', 'approve');
                Route::post('{id}/reject', 'reject');
                Route::get('orders/select2', 'orderSelect');
                Route::get('orders/items/{orderId}', 'getItemsByOrderId');

                Route::get('order/{orderId}/products', 'getProductsByOrder');
                Route::get('get-boms', 'getBoms');
                Route::delete('destroy/{id}', 'destroy');
            }
        );

        Route::prefix('material-imports')->controller(MaterialImportController::class)->name('material-imports.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('show/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
            Route::get('pdf/{id}', 'downloadPdf')->name('downloadPdf');
        });

        Route::prefix('suppliers-debts')->controller(SupplierDebtController::class)->name('suppliers-debts.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('show/{id}', 'show')->name('show');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
            Route::get('pdf/{id}', 'downloadPdf');
            Route::post('pay', 'pay');
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
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
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
            Route::post("/", 'store')->name('store');
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

        Route::get('password-change-request', [PasswordChangeRequestController::class, 'index'])->name('password-change-request.index');
        Route::post('password-change-request-confirm', [PasswordChangeRequestController::class, 'confirm'])->name('password-change-request.confirm');
        Route::post('password-change-request-reject', [PasswordChangeRequestController::class, 'reject'])->name('password-change-request.reject');

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
                Route::post('payment', 'saveConfigPayment');
                Route::delete('payment', 'destroyConfigPayment')->name('destroy.config.payment');
                Route::put('payment/status', 'updateConfigPaymentStatus')->name('update.config.payment.status');
                Route::get('payment/{id}', 'getConfigPayment')->name('get.config.payment');
            }
        );

        Route::prefix('boms')->controller(BomsController::class)->name('boms.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('create', 'create')->name('create');
            Route::post('create', 'store')->name('store');
            Route::get('edit/{id}', 'edit')->name('edit');
            Route::put('edit/{id}', 'update')->name('update');
            Route::get('get-product-select', 'productSelect');
            Route::get('check-variants/{product}', 'checkVariants');
        });
    });

    // Auth Router
    Route::middleware('admin.guest')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::get('login/{token?}', 'login')->name('login');
            Route::get('forgot-password', 'forgotPasswordForm')->name('forgot-password-form');
            Route::post('forgot-password', 'forgotPasswordPost');
            Route::post('login', 'authenticate')->name('login.authenticate');
            Route::get('verify-otp', 'showVerifyOtp')->name('verify-otp');
            Route::post('verify-otp', 'verifyOtp')->name('verify_otp_confirm');
        });
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

    Route::prefix('order')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [OrderController::class, 'detail'])->name('detail');
        // Route::get('/find/phone', [OrderController::class, 'getOrderbyPhone'])->name('findByPhone');
        Route::get('/admin/order/filter', [OrderController::class, 'filterOrder'])->name('filter');
    });

    Route::prefix('quanlythuchi')->name('quanlythuchi.')->group(function () {
        Route::prefix('receipts')->name('receipts.')->group(function () { // phiếu thu
            Route::get('/', [ReceiptController::class, 'index'])->name('index');
            Route::get('/detail/{id}', [ReceiptController::class, 'detail'])->name('detail');
            Route::get('/add', [ReceiptController::class, 'add'])->name('add');
            Route::post('/add', [ReceiptController::class, 'addSubmit'])->name('addSubmit');
            Route::post('/debt', [ReceiptController::class, 'debt'])->name('debt');
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
    });

    Route::prefix('warehouse')->name('warehouse.')->group(function () {
        Route::get('', [WarehouseController::class, 'index'])->name('index');
        Route::get('create', [WarehouseController::class, 'create'])->name('create');
        Route::get('show/{id}', [WarehouseController::class, 'show'])->name('show');
    });
});

Route::post('/submit-table', [WarehouseController::class, 'store']);
