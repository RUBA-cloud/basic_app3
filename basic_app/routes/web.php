<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Verified;

use App\Http\Middleware\SetLocale;

use App\Models\User;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\CompanyBranchController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\AdditionalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferTypeController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\CompanyDeliveryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DeviceTokenController;

/** Root -> login */
Route::redirect('/', '/login')->name('root');

/** Locale wrapper */
Route::middleware([SetLocale::class])->group(function () {

    /** Laravel-UI auth (NO built-in verify to avoid conflicts) */
    Auth::routes([
        'verify'  => false, // we provide custom verify routes below
        // 'reset' => false,
        // 'confirm' => false,
    ]);

    /** Email verify notice page */
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->middleware('auth')->name('verification.notice');

    /** Resend verification email */
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/home');
        }
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    /** Custom verify via signed link (no login required) */
    Route::get('/email/verify/{id}/{hash}', function (Request $request) {
        $user = User::findOrFail($request->route('id'));

        // validate signed hash
        abort_unless(
            hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification())),
            403
        );

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();      // sets email_verified_at
            event(new Verified($user));
        }

        // optional: auto-login after verify
        Auth::login($user);

        return redirect('/home?verified=1');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    /** Language switcher */
    Route::get('/change-language', function (Request $request) {
        $locale = $request->query('locale', 'en');
        if (! in_array($locale, ['en', 'ar'], true)) $locale = 'en';
        session(['locale' => $locale]);

        $target = $request->query('redirect')
            ?: ($request->headers->get('referer') ?: route('home'));

        return redirect()->to($target);
    })->name('change.language');

    /** Auth + Verified area */
    Route::middleware(['auth', 'verified'])->group(function () {

        Route::get('/home', [HomeController::class, 'index'])->name('home');

        /** Categories */
        Route::resource('categories', CategoryController::class);
        Route::put('/reactive_category/{id}', [CategoryController::class, 'reactivate'])->name('reactive_category');
        Route::post('/category-search', [CategoryController::class, 'search'])->name('category-search');
        Route::post('/category-search-history', [CategoryController::class, 'searchHistory'])->name('category-search-history');
        Route::get('/category_history/{isHistory?}', [CategoryController::class, 'index'])->name('category_history');

        /** Company Info */
        Route::resource('companyInfo', CompanyInfoController::class);
        Route::post('/companyInfo_search', [CompanyInfoController::class, 'searchHistory'])->name('companyInfo_search');
        Route::get('/companyInfoHistory', [CompanyInfoController::class, 'history'])->name('company_history');

        /** Company Branch */
        Route::resource('companyBranch', CompanyBranchController::class);
        Route::put('/reactive_branch/{id}', [CompanyBranchController::class, 'reactivate'])->name('reactive_branch');
        Route::post('/companyBranch_search', [CompanyBranchController::class, 'search'])->name('companyBranch_search');
        Route::post('/branch_history/search', [CompanyBranchController::class, 'searchHistory'])->name('branch_history.search');
        Route::get('/branches/{isHistory?}', [CompanyBranchController::class, 'index'])->name('branches.index');

        /** Sizes */
        Route::resource('sizes', SizeController::class);
        Route::get('/sizes_history/{isHistory?}', [SizeController::class, 'index'])->name('sizes.history');
        Route::put('/sizes/reactive/{id}', [SizeController::class, 'reactive'])->name('sizes.reactive');
        Route::post('/size_search_history', [SizeController::class, 'searchHistory'])->name('size_search_history');
        Route::post('/size_search', [SizeController::class, 'search'])->name('sizes.search');

        /** Additional */
        Route::resource('additional', AdditionalController::class);
        Route::get('/additional_history/{isHistory?}', [AdditionalController::class, 'index'])->name('additional.history');
        Route::put('/additional/reactive/{id}', [AdditionalController::class, 'reactive'])->name('additional.reactive');
        Route::post('/additional/search', [AdditionalController::class, 'search'])->name('additional.search');
        Route::post('/additional/search_history', [AdditionalController::class, 'searchHistory'])->name('additional.search_history');

        /** Type */
        Route::resource('type', TypeController::class);
        Route::get('/type_history/{isHistory?}', [TypeController::class, 'index'])->name('type.history');
        Route::put('/type/reactive/{id}', [TypeController::class, 'reactivate'])->name('type.reactive');
        Route::post('/type_search', [TypeController::class, 'search'])->name('type.search');
        Route::post('/type_search_history', [TypeController::class, 'searchHistory'])->name('type.search_history');

        /** Product */
        Route::resource('product', ProductController::class);
        Route::get('/product_history/{isHistory?}', [ProductController::class, 'index'])->name('product.history');
        Route::put('/product/reactive/{id}', [ProductController::class, 'reactivate'])->name('product.reactive');
        Route::post('/product_search', [ProductController::class, 'search'])->name('product.search');
        Route::post('/product_search_history', [ProductController::class, 'searchHistory'])->name('product_history.search');

        /** Offer Type */
        Route::resource('offers_type', OfferTypeController::class);
        Route::get('/offers_type_history/{isHistory?}', [OfferTypeController::class, 'index'])->name('offer_type.history');
        Route::put('/offers_type/reactive/{id}', [OfferTypeController::class, 'reactive'])->name('offer_type.reactive');
        Route::post('/offer_type_search', [OfferTypeController::class, 'search'])->name('offer_type.search');
        Route::post('/offer_search_type_history', [OfferTypeController::class, 'searchHistory'])->name('offer_type.search_history');

        /** Permissions */
        Route::resource('permissions', PermissionController::class);

        /** Offers */
        Route::resource('offers', OfferController::class);
        Route::get('/offers/history/{isHistory?}', [OfferController::class, 'index'])->name('offers.history');
        Route::put('/offers_reactive/{id}', [OfferController::class, 'reactive'])->name('offers.reactive');
        Route::post('/offer_search', [OfferController::class, 'search'])->name('offer.search');
        Route::post('/offer_search_history', [OfferController::class, 'searchHistory'])->name('offer.search_history');

        /** Modules */
        Route::resource('modules', ModulesController::class);
        Route::post('modules/search', [ModulesController::class, 'index'])->name('modules.search');

        /** Employees */
        Route::resource('employees', EmployeeController::class);
        Route::get('/employees/history', [EmployeeController::class, 'history'])->name('employees.history');
        Route::put('/employees/history/{history}/reactivate', [EmployeeController::class, 'reactivate'])->name('employees.reactivate');

        /** Device Tokens */
        Route::post('/device-tokens', [DeviceTokenController::class, 'store'])->name('device-tokens.store');

        /** Orders */
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/history', [OrderController::class, 'history'])->name('orders.history');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::delete('/orders/{order}/items/{item}', [OrderController::class, 'destroyItem'])->name('orders.items.destroy');

        /** Notifications */
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
        Route::post('/notifications/{notification}/mark', [NotificationController::class, 'mark'])->name('notifications.mark');
        Route::post('/notifications/mark-all', [NotificationController::class, 'markAll'])->name('notifications.markAll');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        /** Payment */
        Route::resource('payment', PaymentController::class);
        Route::prefix('payment')->name('payment.')->group(function () {
            Route::post('search', [PaymentController::class, 'search'])->name('search');
            Route::post('restore', [PaymentController::class, 'restore'])->name('restore');
            Route::get('history', [PaymentController::class, 'history'])->name('history');
        });

        /** Company Delivery */
        Route::resource('company_delivery', CompanyDeliveryController::class);
        Route::prefix('company_delivery')->name('company_delivery.')->group(function () {
            Route::post('search', [CompanyDeliveryController::class, 'search'])->name('search');
            Route::post('restore', [CompanyDeliveryController::class, 'restore'])->name('restore');
            Route::get('history', [CompanyDeliveryController::class, 'history'])->name('history');
        });

        /** Order Status */
        Route::resource('order_status', OrderStatusController::class);
        Route::prefix('order_status')->name('order_status.')->group(function () {
            Route::get('history', [OrderStatusController::class, 'history'])->name('history');
            Route::post('search', [OrderStatusController::class, 'search'])->name('search');
            Route::post('restore', [OrderStatusController::class, 'restore'])->name('restore');
        });
    });
});
