<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

use App\Http\Middleware\SetLocale;
use App\Models\User;

// Controllers
use App\Http\Controllers\{
    HomeController, CategoryController, CompanyInfoController, CompanyBranchController,
    EmployeeController, SizeController, AdditionalController, ProductController,
    OfferController, OfferTypeController, ModulesController, TypeController,
    PermissionController, OrderController, RegionController, PaymentController,
    OrderStatusController, CompanyDeliveryController, NotificationController,
    DeviceTokenController, ProfileController, ChatController
};

/*
|--------------------------------------------------------------------------
| Root → login
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login')->name('root');

/*
|--------------------------------------------------------------------------
| Broadcast auth endpoints (Echo/Pusher)
|--------------------------------------------------------------------------
*/
Broadcast::routes();

/*
|--------------------------------------------------------------------------
| Localized routes wrapper
|--------------------------------------------------------------------------
*/
Route::middleware([SetLocale::class])->group(function () {
    /*
    |----------------------------------------------------------------------
    | Public auth (no module/perm here)
    |----------------------------------------------------------------------
    */
    Auth::routes(['verify' => false]);

    Route::get('/email/verify', fn () => view('auth.verify'))
        ->middleware('auth')->name('verification.notice');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) return redirect()->intended('/home');
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['auth','throttle:6,1'])->name('verification.send');

    Route::get('/email/verify/{id}/{hash}', function (Request $request) {
        $user = User::findOrFail($request->route('id'));
        abort_unless(hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification())), 403);
        if (!$user->hasVerifiedEmail()) { $user->markEmailAsVerified(); event(new Verified($user)); }
        Auth::login($user);
        return redirect('/home?verified=1');
    })->middleware(['signed','throttle:6,1'])->name('verification.verify');

    Route::post('/change-language', function (Request $request) {
        $locale = $request->query('locale', $request->input('locale') ?? 'en');
        if (!in_array($locale, ['en','ar'], true)) $locale = 'en';
        session(['locale' => $locale]);
        $target = $request->query('redirect') ?: ($request->headers->get('referer') ?: route('home'));
        return redirect()->to($target);
    })->name('change.language');

    /*
    |----------------------------------------------------------------------
    | Auth + Verified → All module/permission–gated routes
    |----------------------------------------------------------------------
    */
    Route::middleware(['auth','verified'])->group(function () {

        /* ==============================
         * Dashboard (company_dashboard_module)
         * ============================== */
        Route::get('/home', [HomeController::class, 'index'])
            ->middleware('module:company_dashboard_module')
            ->name('home');

        /* ==============================
         * Company Info (company_info_module)
         * ============================== */
        Route::controller(CompanyInfoController::class)
            ->prefix('companyInfo')
            ->name('companyInfo.')
            ->middleware('module:company_info_module')
            ->group(function () {
                Route::get('/',               'index')->name('index');
                Route::get('/create',         'create')->middleware('perm:company_info,can_add')->name('create');
                Route::post('/',              'store')->middleware('perm:company_info,can_add')->name('store');
                Route::get('/{companyInfo}',  'show')->name('show');
                Route::get('/{companyInfo}/edit','edit')->middleware('perm:company_info,can_edit')->name('edit');
                Route::put('/{companyInfo}',  'update')->middleware('perm:company_info,can_edit')->name('update');
                Route::delete('/{companyInfo}','destroy')->middleware('perm:company_info,can_delete')->name('destroy');

                Route::post('/search',        'searchHistory')->middleware('perm:company_info,can_view_history')->name('search');
                Route::get('/history',        'history')->middleware('perm:company_info,can_view_history')->name('history');
            });

        /* ==============================
         * Company Branch (company_branch_module)
         * ============================== */
        Route::controller(CompanyBranchController::class)
            ->prefix('companyBranch')
            ->name('companyBranch.')
            ->middleware('module:company_branch_module')
            ->group(function () {
                Route::get('/',                'index')->name('index');
                Route::get('/create',          'create')->middleware('perm:branch,can_add')->name('create');
                Route::post('/',               'store')->middleware('perm:branch,can_add')->name('store');
                Route::get('/{companyBranch}', 'show')->name('show');
                Route::get('/{companyBranch}/edit','edit')->middleware('perm:branch,can_edit')->name('edit');
                Route::put('/{companyBranch}', 'update')->middleware('perm:branch,can_edit')->name('update');
                Route::delete('/{companyBranch}','destroy')->middleware('perm:branch,can_delete')->name('destroy');

                Route::put('/reactive/{id}',   'reactivate')->middleware('perm:branch,can_edit')->name('reactivate');
                Route::post('/search',         'search')->middleware('perm:branch,can_view_history')->name('search');
                Route::post('/history/search', 'searchHistory')->middleware('perm:branch,can_view_history')->name('history.search');
                Route::get('/list/{isHistory?}','index')->middleware('perm:branch,can_view_history')->name('list');
            });

        /* ==============================
         * Categories (company_category_module)
         * ============================== */
        Route::controller(CategoryController::class)
            ->prefix('categories')
            ->name('categories.')
            ->middleware('module:company_category_module')
            ->group(function () {
                Route::get('/',            'index')->name('index');
                Route::get('/create',      'create')->middleware('perm:category,can_add')->name('create');
                Route::post('/',           'store')->middleware('perm:category,can_add')->name('store');
                Route::get('/{category}',  'show')->name('show');
                Route::get('/{category}/edit','edit')->middleware('perm:category,can_edit')->name('edit');
                Route::put('/{category}',  'update')->middleware('perm:category,can_edit')->name('update');
                Route::delete('/{category}','destroy')->middleware('perm:category,can_delete')->name('destroy');

                Route::put('/reactive/{id}', 'reactivate')->middleware('perm:category,can_edit')->name('reactivate');
                Route::post('/search',       'search')->middleware('perm:category,can_view_history')->name('search');
                Route::post('/search-history','searchHistory')->middleware('perm:category,can_view_history')->name('search_history');
                Route::get('/history/{isHistory?}', 'index')->middleware('perm:category,can_view_history')->name('history');
            });

        /* ==============================
         * Types (company_type_module)
         * ============================== */
        Route::controller(TypeController::class)
            ->prefix('type')
            ->name('type.')
            ->middleware('module:company_type_module')
            ->group(function () {
                Route::get('/',        'index')->name('index');
                Route::get('/create',  'create')->middleware('perm:type,can_add')->name('create');
                Route::post('/',       'store')->middleware('perm:type,can_add')->name('store');
                Route::get('/{type}',  'show')->name('show');
                Route::get('/{type}/edit','edit')->middleware('perm:type,can_edit')->name('edit');
                Route::put('/{type}',  'update')->middleware('perm:type,can_edit')->name('update');
                Route::delete('/{type}','destroy')->middleware('perm:type,can_delete')->name('destroy');

                Route::get('/history/{isHistory?}', 'index')->middleware('perm:type,can_view_history')->name('history');
                Route::put('/reactive/{id}',        'reactivate')->middleware('perm:type,can_edit')->name('reactive');
                Route::post('/search',              'search')->middleware('perm:type,can_view_history')->name('search');
                Route::post('/search_history',      'searchHistory')->middleware('perm:type,can_view_history')->name('search_history');
            });

        /* ==============================
         * Sizes (company_size_module)
         * ============================== */
        Route::controller(SizeController::class)
            ->prefix('sizes')
            ->name('sizes.')
            ->middleware('module:company_size_module')
            ->group(function () {
                Route::get('/',       'index')->name('index');
                Route::get('/create', 'create')->middleware('perm:size,can_add')->name('create');
                Route::post('/',      'store')->middleware('perm:size,can_add')->name('store');
                Route::get('/{size}','show')->name('show');
                Route::get('/{size}/edit','edit')->middleware('perm:size,can_edit')->name('edit');
                Route::put('/{size}','update')->middleware('perm:size,can_edit')->name('update');
                Route::delete('/{size}','destroy')->middleware('perm:size,can_delete')->name('destroy');

                Route::get('/history/{isHistory?}', 'index')->middleware('perm:size,can_view_history')->name('history');
                Route::put('/reactive/{id}',        'reactive')->middleware('perm:size,can_edit')->name('reactive');
                Route::post('/search_history',      'searchHistory')->middleware('perm:size,can_view_history')->name('search_history');
                Route::post('/search',              'search')->middleware('perm:size,can_view_history')->name('search');
            });

        /* ==============================
         * Offer Types (company_offers_type_module)
         * ============================== */
        Route::controller(OfferTypeController::class)
            ->prefix('offers_type')
            ->name('offers_type.')
            ->middleware('module:company_offers_type_module')
            ->group(function () {
                Route::get('/',              'index')->name('index');
                Route::get('/create',        'create')->middleware('perm:offers_type,can_add')->name('create');
                Route::post('/',             'store')->middleware('perm:offers_type,can_add')->name('store');
                Route::get('/{offers_type}', 'show')->name('show');
                Route::get('/{offers_type}/edit','edit')->middleware('perm:offers_type,can_edit')->name('edit');
                Route::put('/{offers_type}', 'update')->middleware('perm:offers_type,can_edit')->name('update');
                Route::delete('/{offers_type}','destroy')->middleware('perm:offers_type,can_delete')->name('destroy');

                Route::get('/history/{isHistory?}','index')->middleware('perm:offers_type,can_view_history')->name('history');
                Route::put('/reactive/{id}',      'reactive')->middleware('perm:offers_type,can_edit')->name('reactive');
                Route::post('/search',            'search')->middleware('perm:offers_type,can_view_history')->name('search');
                Route::post('/search_history',    'searchHistory')->middleware('perm:offers_type,can_view_history')->name('search_history');
            });

        /* ==============================
         * Offers (company_offers_module)
         * ============================== */
        Route::controller(OfferController::class)
            ->prefix('offers')
            ->name('offers.')
            ->middleware('module:company_offers_module')
            ->group(function () {
                Route::get('/',           'index')->name('index');
                Route::get('/create',     'create')->middleware('perm:offers,can_add')->name('create');
                Route::post('/',          'store')->middleware('perm:offers,can_add')->name('store');
                Route::get('/{offer}',    'show')->name('show');
                Route::get('/{offer}/edit','edit')->middleware('perm:offers,can_edit')->name('edit');
                Route::put('/{offer}',    'update')->middleware('perm:offers,can_edit')->name('update');
                Route::delete('/{offer}', 'destroy')->middleware('perm:offers,can_delete')->name('destroy');

                Route::get('/history/{isHistory?}','index')->middleware('perm:offers,can_view_history')->name('history');
                Route::put('/reactive/{id}',      'reactive')->middleware('perm:offers,can_edit')->name('reactive');
                Route::post('/search',            'search')->middleware('perm:offers,can_view_history')->name('search');
                Route::post('/search_history',    'searchHistory')->middleware('perm:offers,can_view_history')->name('search_history');
            });

        /* ==============================
         * Additional (use its own module flag if you have one)
         * If you *do* have 'additional_module', change the module middleware below to it.
         * ============================== */
        Route::controller(AdditionalController::class)
            ->prefix('additional')
            ->name('additional.')
            ->middleware('module:company_offers_module')
            ->group(function () {
                Route::get('/',             'index')->name('index');
                Route::get('/create',       'create')->middleware('perm:additional,can_add')->name('create');
                Route::post('/',            'store')->middleware('perm:additional,can_add')->name('store');
                Route::get('/{additional}', 'show')->name('show');
                Route::get('/{additional}/edit','edit')->middleware('perm:additional,can_edit')->name('edit');
                Route::put('/{additional}', 'update')->middleware('perm:additional,can_edit')->name('update');
                Route::delete('/{additional}','destroy')->middleware('perm:additional,can_delete')->name('destroy');

                Route::get('/history/{isHistory?}','index')->middleware('perm:additional,can_view_history')->name('history');
                Route::put('/reactive/{id}',      'reactive')->middleware('perm:additional,can_edit')->name('reactive');
                Route::post('/search',            'search')->middleware('perm:additional,can_view_history')->name('search');
                Route::post('/search_history',    'searchHistory')->middleware('perm:additional,can_view_history')->name('search_history');
            });

        /* ==============================
         * Product (product_module)
         * ============================== */
        Route::controller(ProductController::class)
            ->prefix('product')
            ->name('product.')
            ->middleware('module:product_module')
            ->group(function () {
                Route::get('/',          'index')->name('index');
                Route::get('/create',    'create')->middleware('perm:product,can_add')->name('create');
                Route::post('/',         'store')->middleware('perm:product,can_add')->name('store');
                Route::get('/{product}', 'show')->name('show');
                Route::get('/{product}/edit','edit')->middleware('perm:product,can_edit')->name('edit');
                Route::put('/{product}', 'update')->middleware('perm:product,can_edit')->name('update');
                Route::delete('/{product}','destroy')->middleware('perm:product,can_delete')->name('destroy');

                Route::get('/history/{isHistory?}','index')->middleware('perm:product,can_view_history')->name('history');
                Route::put('/reactive/{id}',      'reactivate')->middleware('perm:product,can_edit')->name('reactive');
                Route::post('/search',            'search')->middleware('perm:product,can_view_history')->name('search');
                Route::post('/search_history',    'searchHistory')->middleware('perm:product,can_view_history')->name('search_history');
            });

        /* ==============================
         * Employees (employee_module)
         * ============================== */
        Route::controller(EmployeeController::class)
            ->prefix('employees')
            ->name('employees.')
            ->middleware('module:employee_module')
            ->group(function () {
                Route::get('/',       'index')->name('index');
                Route::get('/create', 'create')->middleware('perm:employees,can_add')->name('create');
                Route::post('/',      'store')->middleware('perm:employees,can_add')->name('store');
                Route::get('/{employee}','show')->name('show');
                Route::get('/{employee}/edit','edit')->middleware('perm:employees,can_edit')->name('edit');
                Route::put('/{employee}','update')->middleware('perm:employees,can_edit')->name('update');
                Route::delete('/{employee}','destroy')->middleware('perm:employees,can_delete')->name('destroy');

                Route::get('/history',               'history')->middleware('perm:employees,can_view_history')->name('history');
                Route::put('/history/{history}/reactivate', 'reactivate')->middleware('perm:employees,can_edit')->name('reactivate');
            });

        /* ==============================
         * Orders (order_module)
         * ============================== */
        Route::prefix('orders')
            ->middleware('module:order_module')
            ->group(function () {
                Route::get('/',                 [OrderController::class, 'index'])->name('orders.index');
                Route::get('/history',          [OrderController::class, 'history'])->middleware('perm:order,can_view_history')->name('orders.history');
                Route::get('/{order}',          [OrderController::class, 'show'])->name('orders.show');
                Route::get('/{order}/edit',     [OrderController::class, 'edit'])->middleware('perm:order,can_edit')->name('orders.edit');
                Route::put('/{order}',          [OrderController::class, 'update'])->middleware('perm:order,can_edit')->name('orders.update');
                Route::delete('/{order}',       [OrderController::class, 'destroy'])->middleware('perm:order,can_delete')->name('orders.destroy');
                Route::delete('/{order}/items/{item}', [OrderController::class, 'destroyItem'])->middleware('perm:order,can_delete')->name('orders.items.destroy');
            });

        /* ==============================
         * Regions (region_module)
         * ============================== */
        Route::controller(RegionController::class)
            ->prefix('regions')
            ->name('regions.')
            ->middleware('module:region_module')
            ->group(function () {
                Route::get('/',          'index')->name('index');
                Route::get('/create',    'create')->middleware('perm:regions,can_add')->name('create');
                Route::post('/',         'store')->middleware('perm:regions,can_add')->name('store');
                Route::get('/{region}',  'show')->name('show');
                Route::get('/{region}/edit','edit')->middleware('perm:regions,can_edit')->name('edit');
                Route::put('/{region}',  'update')->middleware('perm:regions,can_edit')->name('update');
                Route::delete('/{region}','destroy')->middleware('perm:regions,can_delete')->name('destroy');

                Route::put('/reactive/{id}', 'reactive')->middleware('perm:regions,can_edit')->name('reactive');
                Route::get('/history/{isHistory?}', 'index')->middleware('perm:regions,can_view_history')->name('history');
                Route::post('/search',          'search')->middleware('perm:regions,can_view_history')->name('search');
                Route::post('/search_history',  'searchHistory')->middleware('perm:regions,can_view_history')->name('search_history');
            });

        /* ==============================
         * Payment (payment_module)
         * ============================== */
        Route::prefix('payment')
            ->name('payment.')
            ->middleware('module:payment_module')
            ->group(function () {
                Route::get('/',               [PaymentController::class, 'index'])->name('index');
                Route::get('/create',         [PaymentController::class, 'create'])->middleware('perm:payment,can_add')->name('create');
                Route::post('/',              [PaymentController::class, 'store'])->middleware('perm:payment,can_add')->name('store');
                Route::get('/{payment}',      [PaymentController::class, 'show'])->name('show');
                Route::get('/{payment}/edit', [PaymentController::class, 'edit'])->middleware('perm:payment,can_edit')->name('edit');
                Route::put('/{payment}',      [PaymentController::class, 'update'])->middleware('perm:payment,can_edit')->name('update');
                Route::delete('/{payment}',   [PaymentController::class, 'destroy'])->middleware('perm:payment,can_delete')->name('destroy');

                Route::post('/search',        [PaymentController::class, 'search'])->middleware('perm:payment,can_view_history')->name('search');
                Route::post('/restore',       [PaymentController::class, 'restore'])->middleware('perm:payment,can_edit')->name('restore');
                Route::get('/history',        [PaymentController::class, 'history'])->middleware('perm:payment,can_view_history')->name('history');
            });

        /* ==============================
         * Company Delivery (company_delivery_module)
         * ============================== */
        Route::controller(CompanyDeliveryController::class)
            ->prefix('company_delivery')
            ->name('company_delivery.')
            ->middleware('module:company_delivery_module')
            ->group(function () {
                Route::get('/',                 'index')->name('index');
                Route::get('/create',           'create')->middleware('perm:company_delivery,can_add')->name('create');
                Route::post('/',                'store')->middleware('perm:company_delivery,can_add')->name('store');
                Route::get('/{company_delivery}','show')->name('show');
                Route::get('/{company_delivery}/edit','edit')->middleware('perm:company_delivery,can_edit')->name('edit');
                Route::put('/{company_delivery}','update')->middleware('perm:company_delivery,can_edit')->name('update');
                Route::delete('/{company_delivery}','destroy')->middleware('perm:company_delivery,can_delete')->name('destroy');

                Route::post('/search',          'search')->middleware('perm:company_delivery,can_view_history')->name('search');
                Route::post('/restore',         'restore')->middleware('perm:company_delivery,can_edit')->name('restore');
                Route::get('/history',          'history')->middleware('perm:company_delivery,can_view_history')->name('history');
            });

        /* ==============================
         * Order Status (order_status_module)
         * ============================== */
        Route::controller(OrderStatusController::class)
            ->prefix('order_status')
            ->name('order_status.')
            ->middleware('module:order_status_module')
            ->group(function () {
                Route::get('/',               'index')->name('index');
                Route::get('/create',         'create')->middleware('perm:order_status,can_add')->name('create');
                Route::post('/',              'store')->middleware('perm:order_status,can_add')->name('store');
                Route::get('/{order_status}', 'show')->name('show');
                Route::get('/{order_status}/edit','edit')->middleware('perm:order_status,can_edit')->name('edit');
                Route::put('/{order_status}', 'update')->middleware('perm:order_status,can_edit')->name('update');
                Route::delete('/{order_status}','destroy')->middleware('perm:order_status,can_delete')->name('destroy');

                Route::get('/history',        'history')->middleware('perm:order_status,can_view_history')->name('history');
                Route::post('/search',        'search')->middleware('perm:order_status,can_view_history')->name('search');
                Route::post('/restore',       'restore')->middleware('perm:order_status,can_edit')->name('restore');
            });

        /* ==============================
         * Modules & Permissions (admin area; change the gate if needed)
         * ============================== */
        Route::controller(ModulesController::class)
            ->prefix('modules')
            ->name('modules.')
            ->middleware('module:company_dashboard_module')
            ->group(function () {
                Route::get('/',       'index')->name('index');
                Route::get('/create', 'create')->name('create')->middleware('perm:modules,can_add');
                Route::post('/',      'store')->name('store')->middleware('perm:modules,can_add');
                Route::get('/{module}','show')->name('show');
                Route::get('/{module}/edit','edit')->name('edit')->middleware('perm:modules,can_edit');
                Route::put('/{module}','update')->name('update')->middleware('perm:modules,can_edit');
                Route::delete('/{module}','destroy')->name('destroy')->middleware('perm:modules,can_delete');

                Route::post('/search','index')->name('search')->middleware('perm:modules,can_view_history');
            });

        Route::controller(PermissionController::class)
            ->prefix('permissions')
            ->name('permissions.')
            ->middleware('module:company_dashboard_module')
            ->group(function () {
                Route::get('/',           'index')->name('index');
                Route::get('/create',     'create')->middleware('perm:permissions,can_add')->name('create');
                Route::post('/',          'store')->middleware('perm:permissions,can_add')->name('store');
                Route::get('/{permission}','show')->name('show');
                Route::get('/{permission}/edit','edit')->middleware('perm:permissions,can_edit')->name('edit');
                Route::put('/{permission}','update')->middleware('perm:permissions,can_edit')->name('update');
                Route::delete('/{permission}','destroy')->middleware('perm:permissions,can_delete')->name('destroy');
            });

        /* ==============================
         * Notifications (gate via dashboard)
         * ============================== */
        Route::controller(NotificationController::class)
            ->prefix('notifications')
            ->name('notifications.')
            ->middleware('module:company_dashboard_module')
            ->group(function () {
                Route::get('/',           'index')->name('index');
                Route::post('/',          'store')->name('store');
                Route::post('/{notification}/mark','mark')->name('mark');
                Route::post('/mark-all',  'markAll')->name('markAll');
                Route::delete('/{notification}','destroy')->name('destroy');
            });

        /* ==============================
         * Device Tokens (FCM) — behind auth; add module if you want
         * ============================== */
        Route::post('/device-tokens', [DeviceTokenController::class, 'store'])
            ->name('device-tokens.store');

        /* ==============================
         * Profile (auth only)
         * ============================== */
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        /* ==============================
         * Chat (gate via dashboard or create a chat_module flag)
         * ============================== */
        Route::controller(ChatController::class)
            ->prefix('chat')
            ->name('chat.')
            ->middleware('module:company_dashboard_module')
            ->group(function () {
                Route::get('/',      'index')->name('index');
                Route::post('/',     'store')->name('store');          // if you have store
                Route::get('/{id}',  'show')->name('show');            // adjust if needed
            });
    });
});
