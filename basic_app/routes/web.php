
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Middleware\SetLocale;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
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
// Auth routes with email verification
Auth::routes(['verify' => true]);

// Remove LoginController as a resource controller (login is already handled by Auth::routes())
// If you really need custom login logic, use explicit routes instead

// Dashboard rout–
Route::get('/', function () {
    return redirect()->route('login');
});
// Email Verification routes
Route::get('/verify', [VerificationController::class, 'verify'])->name('verify');
Route::post('/resend-verification', [VerificationController::class, 'resend'])->name('verify.resend');
Route::put('reactive_branch/{id}', [CompanyBranchController::class, 'reactivate'])->name('reactive_branch');
Route::put('reactive_category/{id}', [CategoryController::class, 'reactivate'])->name('reactive_category');



Route::group(['middleware' => [SetLocale::class]], function () {

    // Your normal routes here
    Route::get('/',[HomeController::class, 'index'])->name('home');

    // Language switch route
    Route::get('/change-language/{lang}', function ($lang) {
        if (in_array($lang, ['en', 'ar'])) {
            session(['locale' => $lang]);
            app()->setLocale($lang);
            App::setLocale($lang);
            return redirect()->back();
        }
    })->name('change.language');



Route::resource('categories', CategoryController::class);
// Home route
Route::get('/home/{lang}', [HomeController::class, 'index'])->name('home');
// Company Info routes
Route::resource('companyInfo', CompanyInfoController::class);
// Company Branch routes
Route::resource('companyBranch', CompanyBranchController::class);
Route::get('/branches/{isHistory?}', [CompanyBranchController::class, 'index'])->name('branches.index');
Route::get('/category_history/{isHistory?}', [CategoryController::class, 'index'])->name('category_history');
Route::get('/companyInfoHistory', [CompanyInfoController::class, 'history'])->name('company_histroy');
Route::resource('sizes', SizeController::class);
Route::get('/sizes_history/{isHistory?}', [SizeController::class, 'index'])->name('sizes.history');
Route::put('/reactive/{id?}', [SizeController::class, 'reactive'])->name('sizes.reactive');
Route::resource('additional', AdditonalController::class);
Route::get('/additional_history/{isHistory?}', [AdditonalController::class, 'index'])->name('additional.history');
Route::put('/reactive/{id?}', [AdditonalController::class, 'reactive'])->name('additional.reactive');
Route::resource('type', TypeController::class);
Route::get('/type_history/{isHistory?}', [TypeController::class, 'index'])->name('type.history');
Route::put('/reactive/{id?}', [TypeController::class, 'reactivate'])->name('type.reactive');
Route::resource('product', ProductController::class);
Route::put('/reactive/{id?}', [ProductController::class, 'reactivate'])->name('product.reactive');
Route::get('/product_history/{isHistory?}', [ProductController::class, 'index'])->name('product.history');
Route::get('users/{id}', [UserController::class, 'index'])->name('user.index');
Route::resource('offers_type',OfferTypeController::class);
Route::get('/s_history/{isHistory?}', [OfferTypeController::class, 'index'])->name('offer_type.history');
Route::put('/offer_type_reactive/{id?}',[OfferTypeController::class,'reactive'])->name('offer_type_reactive');


});

