<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Portal;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\SiteTemplateController;
use App\Http\Controllers\TemplatePreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/templates', [SiteTemplateController::class, 'index'])->name('templates.index');
Route::get('/templates/{siteTemplate}', [SiteTemplateController::class, 'show'])->name('templates.show');
Route::get('/templates/{siteTemplate}/preview', TemplatePreviewController::class)->name('templates.preview');
Route::view('/pricing', 'pages.pricing')->name('pricing');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
Route::get('/about', AboutController::class)->name('about');
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:forms')->name('contact.store');
Route::view('/terms', 'pages.legal.terms')->name('legal.terms');
Route::view('/privacy', 'pages.legal.privacy')->name('legal.privacy');
Route::view('/cancellation-policy', 'pages.legal.cancellation')->name('legal.cancellation');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/templates/{siteTemplate}/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/templates/{siteTemplate}/checkout', [CheckoutController::class, 'store'])->middleware('throttle:forms')->name('checkout.store');

    Route::prefix('account')->name('portal.')->group(function () {
        Route::get('/', Portal\DashboardController::class)->name('dashboard');
        Route::get('/billing', Portal\BillingPortalController::class)->name('billing');
        Route::get('/orders/{order}', [Portal\OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/intake', [Portal\IntakeSubmissionController::class, 'create'])->name('orders.intake.create');
        Route::post('/orders/{order}/intake', [Portal\IntakeSubmissionController::class, 'store'])->name('orders.intake.store');
        Route::post('/orders/{order}/edit-requests', [Portal\EditRequestController::class, 'store'])->middleware('throttle:forms')->name('orders.edit-requests.store');
        Route::post('/orders/{order}/cancellation', [Portal\OrderCancellationController::class, 'store'])->name('orders.cancellation.store');
    });
});

Route::middleware(['auth', 'verified', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::resource('site-templates', Admin\SiteTemplateController::class)->except('show');
    Route::resource('portfolio-items', Admin\PortfolioItemController::class)->except('show');
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::get('/orders/{order}/file', [Admin\OrderFileController::class, 'show'])->name('orders.file');
    Route::resource('edit-requests', Admin\EditRequestController::class)->only(['index', 'update']);
    Route::resource('contact-messages', Admin\ContactMessageController::class)->only(['index', 'show', 'destroy']);
    Route::get('/about', [Admin\AboutController::class, 'edit'])->name('about.edit');
    Route::put('/about', [Admin\AboutController::class, 'update'])->name('about.update');
});

require __DIR__.'/auth.php';
