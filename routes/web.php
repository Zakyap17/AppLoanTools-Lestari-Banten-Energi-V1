<?php

use Illuminate\Support\Facades\Route;

/*Dashboard*/
use App\Http\Controllers\DashboardController;

/*Master Tools*/
use App\Http\Controllers\Master\LocationsController;
use App\Http\Controllers\Master\RackController;
use App\Http\Controllers\Master\ToolsController;
use App\Http\Controllers\Master\ToolsTypeController;
use App\Http\Controllers\Master\ToolsMatGroupController;
use App\Http\Controllers\Master\ToolsMatSubGroupController;
use App\Http\Controllers\Master\ManufacturerController;
use App\Http\Controllers\Master\VendorCalibrationController;

/*Tools Transaction*/
use App\Http\Controllers\Transaction\LoanController;
use App\Http\Controllers\Transaction\DamageLostController;
use App\Http\Controllers\Transaction\ReceiveController;
use App\Http\Controllers\Transaction\CalibrationIssuingController;
use App\Http\Controllers\Transaction\MutationController;

/*Report*/
use App\Http\Controllers\Report\ReportingCenterController;

/*Employe*/
use App\Http\Controllers\EmployeeController;

/* Login & Logout */
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', [LoginController::class, 'logout']); // Fallback for GET requests

// Keep-alive endpoint to refresh session
Route::post('/keep-alive', [LoginController::class, 'keepAlive'])->middleware('auth');

Route::get('/test-email', function () {
    $to = request('to');
    if (!$to) {
        return "Tolong tambahkan tujuan email. Contoh: /test-email?to=nama@gmail.com";
    }

    try {
        \Illuminate\Support\Facades\Mail::to($to)->send(new \App\Mail\TestMail());
        return "Berhasil mengirim email percobaan ke " . $to . "! Coba cek inbox atau folder Spam.";
    } catch (\Exception $e) {
        return "Gagal mengirim email: " . $e->getMessage();
    }
});

/* Authenticated Routes */
Route::middleware(['auth'])->group(function () {
    /* Change Password */
    Route::get('/change-password', [PasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'update'])->name('password.update');

    /* Home */

    Route::redirect('/', '/dashboard');

    /*Dashboard*/
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::post('/dashboard/clear-notifications', [DashboardController::class, 'clearNotifications'])
        ->name('notifications.clear');
    /* Master Tools */
    // Locations
    Route::get('/locations/template', [LocationsController::class, 'template'])->name('locations.template');
    Route::post('/locations/import', [LocationsController::class, 'import'])->name('locations.import');
    Route::resource('/locations', LocationsController::class)
        ->names([
            'index' => 'locations.index',
            'create' => 'locations.create',
            'store' => 'locations.store',
            'show' => 'locations.show',
            'edit' => 'locations.edit',
            'update' => 'locations.update',
            'destroy' => 'locations.destroy',
        ]);

    // Racks Routes
    Route::resource('racks', RackController::class)->only(['edit', 'update', 'destroy']);

    Route::get('/tools/get-bins/{id}', [ToolsController::class, 'getBins'])->name('tools.get-bins');
    Route::get('/tools/template', [ToolsController::class, 'template'])->name('tools.template');
    Route::post('/tools/import', [ToolsController::class, 'import'])->name('tools.import');
    Route::get('/tools/{tool}/good-receipt', [ToolsController::class, 'goodReceipt'])->name('tools.good-receipt');
    Route::get('/tools/print-batch/{batchId}', [ToolsController::class, 'printBatch'])->name('tools.print-batch');
    Route::resource('/tools', ToolsController::class)

        ->names([
            'index' => 'tools.index',
            'create' => 'tools.create',
            'store' => 'tools.store',
            'show' => 'tools.show',
            'edit' => 'tools.edit',
            'update' => 'tools.update',
            'destroy' => 'tools.destroy',
        ]);

    Route::get('/tools-type/template', [ToolsTypeController::class, 'template'])->name('tools-type.template');
    Route::post('/tools-type/import', [ToolsTypeController::class, 'import'])->name('tools-type.import');
    Route::resource('/tools-type', ToolsTypeController::class)
        ->names([
            'index' => 'tools-type.index',
            'create' => 'tools-type.create',
            'store' => 'tools-type.store',
            'show' => 'tools-type.show',
            'edit' => 'tools-type.edit',
            'update' => 'tools-type.update',
            'destroy' => 'tools-type.destroy',
        ]);

    Route::get('/group/template', [ToolsMatGroupController::class, 'template'])->name('tools-mat-group.template');
    Route::post('/group/import', [ToolsMatGroupController::class, 'import'])->name('tools-mat-group.import');
    Route::resource('/group', ToolsMatGroupController::class)
        ->names([
            'index' => 'tools-mat-group.index',
            'create' => 'tools-mat-group.create',
            'store' => 'tools-mat-group.store',
            'edit' => 'tools-mat-group.edit',
            'update' => 'tools-mat-group.update',
            'destroy' => 'tools-mat-group.destroy',
        ]);

    Route::resource('/sub-group', ToolsMatSubGroupController::class)
        ->names([
            'index' => 'tools-mat-sub-group.index',
            'create' => 'tools-mat-sub-group.create',
            'store' => 'tools-mat-sub-group.store',
            'edit' => 'tools-mat-sub-group.edit',
            'update' => 'tools-mat-sub-group.update',
            'destroy' => 'tools-mat-sub-group.destroy',
        ]);

    Route::get('/manufacture/template', [ManufacturerController::class, 'template'])->name('manufacture.template');
    Route::post('/manufacture/import', [ManufacturerController::class, 'import'])->name('manufacture.import');
    Route::resource('/manufacture', ManufacturerController::class)
        ->names([
            'index' => 'manufacture.index',
            'create' => 'manufacture.create',
            'store' => 'manufacture.store',
            'show' => 'manufacture.show',
            'edit' => 'manufacture.edit',
            'update' => 'manufacture.update',
            'destroy' => 'manufacture.destroy',
        ]);

    Route::get('/vendors-calibration/template', [VendorCalibrationController::class, 'template'])->name('vendors-calibration.template');
    Route::post('/vendors-calibration/import', [VendorCalibrationController::class, 'import'])->name('vendors-calibration.import');
    Route::resource('/vendors-calibration', VendorCalibrationController::class)
        ->names([
            'index' => 'vendors-calibration',
            'create' => 'vendors-calibration.create',
            'store' => 'vendors-calibration.store',
            'edit' => 'vendors-calibration.edit',
            'update' => 'vendors-calibration.update',
            'destroy' => 'vendors-calibration.destroy',
        ]);

    /* Transaction */
    Route::get('/loan/search-asset', [LoanController::class, 'searchAsset'])->name('loan.search-asset');
    Route::get('/loan/search', [LoanController::class, 'searchAsset'])->name('loan.search');
    Route::get('/loan/items/{toolId}', [LoanController::class, 'getAvailableItems'])->name('loan.items');
    Route::get('/loan/{loanCode}/print', [LoanController::class, 'print'])->name('loan.print'); // NEW
    Route::post('/loan/{id}/return', [LoanController::class, 'processReturn'])->name('loan.return');
    Route::resource('/loan', LoanController::class)->names([
        'index' => 'loan.index',
        'create' => 'loan.create',
        'store' => 'loan.store',
        'show' => 'loan.show',
        'edit' => 'loan.edit',
        'update' => 'loan.update',
        'destroy' => 'loan.destroy',
    ]);

    Route::get('/damage-lost', [DamageLostController::class, 'index'])->name('damage-lost.index');
    Route::post('/damage-lost', [DamageLostController::class, 'store'])->name('damage-lost.store');
    Route::get('/damage-lost/loan-details/{loanCode}', [DamageLostController::class, 'getLoanDetails']);
    Route::get('/damage-lost/{id}/print', [DamageLostController::class, 'print'])->name('damage-lost.print');
    Route::post('/damage-lost/{id}/resolve', [DamageLostController::class, 'resolve'])->name('damage-lost.resolve');

    Route::get('/receive', [ReceiveController::class, 'index'])
        ->name('receive');

    Route::get('/calibration-issuing/template', [CalibrationIssuingController::class, 'template'])
        ->name('calibration-issuing.template');
    Route::post('/calibration-issuing/import', [CalibrationIssuingController::class, 'import'])
        ->name('calibration-issuing.import');
    Route::get('/calibration-issuing', [CalibrationIssuingController::class, 'index'])
        ->name('calibration-issuing');
    Route::get('/calibration-issuing/create', [CalibrationIssuingController::class, 'create'])
        ->name('calibration-issuing.create');
    Route::post('/calibration-issuing', [CalibrationIssuingController::class, 'store'])
        ->name('calibration-issuing.store');
    Route::post('/calibration-issuing/{id}/complete', [CalibrationIssuingController::class, 'complete'])
        ->name('calibration-issuing.complete');

    Route::get('/mutation/search', [MutationController::class, 'searchAsset'])->name('mutation.search');
    Route::get('/mutation/document/{id}', [MutationController::class, 'viewDocument'])->name('mutation.document');
    Route::get('/mutation', [MutationController::class, 'index'])
        ->name('mutation');
    Route::post('/mutation', [MutationController::class, 'store'])
        ->name('mutation.store');

    /*Report*/
    Route::get('/reporting-center', [ReportingCenterController::class, 'index'])
        ->name('reporting-center');
    Route::get('/reporting-center/export', [ReportingCenterController::class, 'exportExcel'])
        ->name('reporting-center.export');

    /* Employee */
    Route::get('/employee/template', [EmployeeController::class, 'template'])->name('employee.template');
    Route::post('/employee/import', [EmployeeController::class, 'import'])->name('employee.import');
    Route::post('/employee/sync-ldap', [EmployeeController::class, 'syncFromLdap'])->name('employee.sync-ldap');
    Route::resource('/employee', EmployeeController::class)
        ->names([
            'index' => 'employee.index',
            'create' => 'employee.create',
            'store' => 'employee.store',
            'show' => 'employee.show',
            'edit' => 'employee.edit',
            'update' => 'employee.update',
            'destroy' => 'employee.destroy',
        ]);

});



