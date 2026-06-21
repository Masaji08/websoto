<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentHistoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Kasir\KasirOrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Publik - Menu Pelanggan
Route::prefix('menu/{table:slug}')->name('menu.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('index');
    Route::get('/checkout', [MenuController::class, 'checkout'])->name('checkout');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store')->middleware('throttle:10,1');
    Route::get('/order/{orderNumber}', [OrderController::class, 'status'])->name('order.status');
    Route::get('/order/{orderNumber}/pay', [OrderController::class, 'retryPayment'])->name('order.pay');
    Route::post('/order/{orderNumber}/cancel', [OrderController::class, 'cancelOrder'])->name('order.cancel');
    Route::post('/order/{orderNumber}/check-payment', [OrderController::class, 'checkPayment'])->name('order.check-payment');
});

// Midtrans Callback
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');

// Kasir Dashboard
Route::middleware(['auth', 'role:cashier,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/orders', [KasirOrderController::class, 'index'])->name('orders');
    Route::patch('/orders/{order}/status', [KasirOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/mark-paid', [KasirOrderController::class, 'markPaid'])->name('orders.mark-paid');
    Route::get('/orders/{order}', [KasirOrderController::class, 'show'])->name('orders.show');
});

// Admin Panel - Admin only
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('tables', TableController::class);
    Route::get('tables/{table}/qr', [TableController::class, 'downloadQr'])->name('tables.qr');
    Route::get('tables/{table}/qr-preview', [TableController::class, 'previewQr'])->name('tables.qr-preview');
    Route::get('reports', [ReportController::class, 'index'])->name('reports');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
    Route::get('payments', [PaymentHistoryController::class, 'index'])->name('payments');
});

// Admin Panel - Cashier & Admin
Route::middleware(['auth', 'role:cashier,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('menu-items', MenuItemController::class)->except('update');
    Route::patch('menu-items/{menu_item}/toggle', [MenuItemController::class, 'toggleAvailability'])->name('menu-items.toggle');
    Route::put('menu-items/{menu_item}', [MenuItemController::class, 'update'])->name('menu-items.update');
    Route::resource('categories', CategoryController::class);
    Route::resource('packages', PackageController::class)->except('show', 'update');
    Route::patch('packages/{package}/toggle', [PackageController::class, 'toggleActive'])->name('packages.toggle');
    Route::put('packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/remove-logo', [SettingsController::class, 'removeLogo'])->name('settings.remove-logo');
});

// Breeze routes (auth, profile)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

