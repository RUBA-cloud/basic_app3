<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\SetLocale;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\AdditonalController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferTypeController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::group(['middleware' => [SetLocale::class]], function () {
    Auth::routes(['verify' => true]);

    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Language switch
    Route::get('/change-language/{lang}', function ($lang) {
        if (in_array($lang, ['en', 'ar'])) {
            session(['locale' => $lang]);
            app()->setLocale($lang);
            App::setLocale($lang);
        }
        return redirect()->back();
    })->name('change.language');

    // Verification
    Route::get('/verify', [VerificationController::class, 'verify'])->name('verify');
    Route::post('/resend-verification', [VerificationController::class, 'resend'])->name('verify.resend');

    // Categories
    Route::put('reactive_category/{id}', [CategoryController::class, 'reactivate'])->name('reactive_category');
    Route::post('category-search', [CategoryController::class, 'search'])->name('category-search');
    Route::post('category-search-history', [CategoryController::class, 'searchHistory'])->name('category-search-history');
    Route::resource('categories', CategoryController::class);
    Route::get('/category_history/{isHistory?}', [CategoryController::class, 'index'])->name('category_history');

    // Company Info
    Route::resource('companyInfo', CompanyInfoController::class);
    Route::post('/companyInfo_search', [CompanyInfoController::class, 'searchHistory'])->name('companyInfo_search');
    Route::get('/companyInfoHistory', [CompanyInfoController::class, 'history'])->name('company_history');

    // Company Branch
    Route::resource('companyBranch', CompanyBranchController::class);
    Route::put('reactive_branch/{id}', [CompanyBranchController::class, 'reactivate'])->name('reactive_branch');
    Route::post('/companyBranch_search', [CompanyBranchController::class, 'search'])->name('companyBranch_search');
    Route::post('/companyBranch_search_history', [CompanyBranchController::class, 'searchHistory'])->name('companyBranch_search_history');
    Route::get('/branches/{isHistory?}', [CompanyBranchController::class, 'index'])->name('branches.index');

    // Sizes
    Route::resource('sizes', SizeController::class);
    Route::get('/sizes_history/{isHistory?}', [SizeController::class, 'index'])->name('sizes.history');
    Route::put('/sizes/reactive/{id}', [SizeController::class, 'reactive'])->name('sizes.reactive');
Route::post('/size_search_history', [SizeController::class, 'searchHistory'])->name('size_search_history');
    Route::post('size_search', [SizeController::class, 'search']);
    // Additional
    Route::resource('additional', AdditonalController::class);
    Route::get('/additional_history/{isHistory?}', [AdditonalController::class, 'index'])->name('additional.history');
    Route::put('/additional/reactive/{id}', [AdditonalController::class, 'reactive'])->name('additional.reactive');

    // Type
    Route::resource('type', TypeController::class);
    Route::get('/type_history/{isHistory?}', [TypeController::class, 'index'])->name('type.history');
    Route::put('/type/reactive/{id}', [TypeController::class, 'reactivate'])->name('type.reactive');

    // Product
    Route::resource('product', ProductController::class);
    Route::get('/product_history/{isHistory?}', [ProductController::class, 'index'])->name('product.history');
    Route::put('/product/reactive/{id}', [ProductController::class, 'reactivate'])->name('product.reactive');

    // Users
    Route::resource('users', UserController::class);

    // Offers Type
    Route::resource('offers_type', OfferTypeController::class);
    Route::get('/offers_type_history/{isHistory?}', [OfferTypeController::class, 'index'])->name('offer_type.history');
    Route::put('/offers_type/reactive/{id}', [OfferTypeController::class, 'reactive'])->name('offer_type.reactive');
});
