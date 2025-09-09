<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\SetLocale;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\EmployeeController;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\AdditionalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferTypeController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OrderController;


Route::get('/', function () {
    return redirect()->route('login');
});

// Wrap everything in SetLocale middleware
Route::middleware([SetLocale::class])->group(function () {

    // Public Auth routes — accessible to guests
    Auth::routes(['verify' => true]);

    // Language Switcher (also public)
    Route::get('/change-language/{lang}', function ($lang) {
        if (in_array($lang, ['en', 'ar'])) {
            session(['locale' => $lang]);
            app()->setLocale($lang);
            App::setLocale($lang);
        }
        return redirect()->back();
    })->name('change.language');

    // All protected routes — must be logged in and email-verified
    Route::middleware(['auth', 'verified'])->group(function () {

        // Home
        Route::get('/home', [HomeController::class, 'index'])->name('home');

        // Email Verification (custom views)
        Route::get('/verify', [VerificationController::class, 'verify'])->name('verify');
        Route::post('/resend-verification', [VerificationController::class, 'resend'])->name('verify.resend');

        // Categories
        Route::resource('categories', CategoryController::class);

        Route::put('/reactive_category/{id}', [CategoryController::class, 'reactivate'])->name('reactive_category');
        Route::post('/category-search', [CategoryController::class, 'search'])->name('category-search');
        Route::post('/category-search-history', [CategoryController::class, 'searchHistory'])->name('category-search-history');
        Route::get('/category_history/{isHistory?}', [CategoryController::class, 'index'])->name('category_history');

        // Company Info
        Route::resource('companyInfo', CompanyInfoController::class);
        Route::post('/companyInfo_search', [CompanyInfoController::class, 'searchHistory'])->name('companyInfo_search');
        Route::get('/companyInfoHistory', [CompanyInfoController::class, 'history'])->name('company_history');

        // Company Branch
        Route::resource('companyBranch', CompanyBranchController::class);
        Route::put('/reactive_branch/{id}', [CompanyBranchController::class, 'reactivate'])->name('reactive_branch');
        Route::post('/companyBranch_search', [CompanyBranchController::class, 'search'])->name('companyBranch_search');
        Route::post('/branch_history/search', [CompanyBranchController::class, 'searchHistory'])->name('branch_history.search');
        Route::get('/branches/{isHistory?}', [CompanyBranchController::class, 'index'])->name('branches.index');

        // Sizes
        Route::resource('sizes', SizeController::class);
        Route::get('/sizes_history/{isHistory?}', [SizeController::class, 'index'])->name('sizes.history');
        Route::put('/sizes/reactive/{id}', [SizeController::class, 'reactive'])->name('sizes.reactive');
        Route::post('/size_search_history', [SizeController::class, 'searchHistory'])->name('size_search_history');
        Route::post('/size_search', [SizeController::class, 'search'])->name('sizes.search');

        // Additional
        Route::resource('additional', AdditionalController::class);
        Route::get('/additional_history/{isHistory?}', [AdditionalController::class, 'index'])->name('additional.history');
        Route::put('/additional/reactive/{id}', [AdditionalController::class, 'reactive'])->name('additional.reactive');
        Route::post('/additional/search', [AdditionalController::class, 'search'])->name('additional.search');
        Route::post('/additional/search_history', [AdditionalController::class, 'searchHistory'])->name('additional.search_history');

        // Type
        Route::resource('type', TypeController::class);
        Route::get('/type_history/{isHistory?}', [TypeController::class, 'index'])->name('type.history');
        Route::put('/type/reactive/{id}', [TypeController::class, 'reactivate'])->name('type.reactive');
        Route::post('/type_search', [TypeController::class, 'search'])->name('type.search');
        Route::post('/type_search_history', [TypeController::class, 'searchHistory'])->name('type.search_history');

        // Product
        Route::resource('product', ProductController::class);
        Route::get('/product_history/{isHistory?}', [ProductController::class, 'index'])->name('product.history');
        Route::put('/product/reactive/{id}', [ProductController::class, 'reactivate'])->name('product.reactive');
        Route::post('/product_search', [ProductController::class, 'search'])->name('product.search');
        Route::post('/product_search_history', [ProductController::class, 'searchHistory'])->name('product_history.search');

        // Offer Type
        Route::resource('offers_type', OfferTypeController::class);
        Route::get('/offers_type_history/{isHistory?}', [OfferTypeController::class, 'index'])->name('offer_type.history');
        Route::put('/offers_type/reactive/{id}', [OfferTypeController::class, 'reactive'])->name('offer_type.reactive');
        Route::post('/offer_type_search', [OfferTypeController::class, 'search'])->name('offer_type.search');
        Route::post('/offer_search_type_history', [OfferTypeController::class, 'searchHistory'])->name('offer_type.search_history');
        //Permissions
        Route::resource('permissions', PermissionController::class);
        // Offers
        Route::resource('offers', OfferController::class);
        Route::get('/offers/{isHistory?}', [OfferController::class, 'index'])->name('offers.history');
        Route::put('/offers_reactive/{id}', [OfferController::class, 'reactive'])->name('offers.reactive');
        Route::post('/offer_search', [OfferController::class, 'search'])->name('offer.search');
        Route::post('/offer_search_history', [OfferController::class, 'searchHistory'])->name('offer.search_history');
        Route::resource('/modules', ModulesController::class);
        Route::post('modules/search',[ModulesController::class,'index'])->name('modules.search');
        Route::resource('employees', EmployeeController::class);
        Route::get('employee_history',['EmployeeController@history'] )->name('employees.history');
        // routes/web.php
Route::put('/employees/history/{history}/reactivate', [EmployeeController::class, 'reactivate'])
    ->name('employees.reactivate');

// routes/web.php
Route::post('/device-tokens', [\App\Http\Controllers\DeviceTokenController::class, 'store'])->name('device-tokens.store')->middleware('auth');
// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::get('/orders',                [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',        [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit',   [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}',        [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}',     [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/orders/history',[OrderController::class, 'history'])->name('orders.history');
    Route::delete('/orders/{order}/items/{item}', [OrderController::class, 'destroyItem'])
    ->name('orders.items.destroy');
});

}); // end SetLocale grou

});
