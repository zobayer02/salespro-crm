<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApiIntegrationController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CustomerAssignmentController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\KpiOverviewController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReengagementController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetOtpController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/forgot-password', [PasswordResetOtpController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetOtpController::class, 'store'])->name('password.email');
    Route::get('/verify-password-code', [PasswordResetOtpController::class, 'verify'])->name('password.otp');
    Route::post('/verify-password-code', [PasswordResetOtpController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::get('/reset-password', [PasswordResetOtpController::class, 'resetForm'])->name('password.reset.form');
    Route::post('/reset-password', [PasswordResetOtpController::class, 'reset'])->name('password.update');
    Route::get('/password-reset-success', [PasswordResetOtpController::class, 'success'])->name('password.reset.success');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/dashboard', '/admin/dashboard')->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'owner'])
    ->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');
        Route::resource('products', ProductController::class)->except(['show']);
        Route::get('inventory', InventoryController::class)->name('inventory.index');
        Route::resource('branches', BranchController::class)->except(['show', 'destroy']);
        Route::get('invoices', InvoiceController::class)->name('invoices.index');
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
        Route::get('customers/inactive', [CustomerController::class, 'inactive'])->name('customers.inactive');
        Route::resource('customers', CustomerController::class);
        Route::resource('employees', EmployeeController::class)->except(['show']);
        Route::resource('assignments', CustomerAssignmentController::class)->only(['index', 'create', 'store']);
        Route::resource('reengagements', ReengagementController::class)->only(['index', 'store']);
        Route::get('kpi-overview', KpiOverviewController::class)->name('kpi.index');
        Route::get('api-integrations', ApiIntegrationController::class)->name('api-integrations.index');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    });
