<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Country;
use App\Models\City;
use App\Models\TraspartationWay;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
class AuthApiController extends Controller
{
    protected Configuration $jwtConfig;

    public function __construct()
    {
        $secret = (string) env('JWT_SECRET', 's+rZafHSdf+PWmoNYMjNrM33YbAjdb0q59mMN4i2TQg');

        $this->jwtConfig = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($secret)
        );
    }

    /**
     * Register a new user + send email verification.
     */
    public function register(Request $request)
    {
        $rules = [
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'password'     => 'required|string|min:6',
            'device_token' => 'nullable|string',
            'language'     => 'nullable|string|in:en,ar',
        ];

        if (Schema::hasColumn('users', 'phone')) {
            $rules['phone'] = [
                'required', 'string', 'max:255',
                Rule::unique('users', 'phone'),
                'regex:/^\+?[0-9\s\-\(\)]{7,20}$/',
            ];
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attrs = [
            'name'              => (string) $request->string('name'),
            'email'             => (string) $request->string('email'),
            'password'          => Hash::make((string) $request->string('password')),
            'email_verified_at' => null,
        ];

        if (Schema::hasColumn('users', 'phone') && $request->filled('phone')) {
            $attrs['phone'] = (string) $request->string('phone');
        }

        /** @var \App\Models\User $user */
        $user = User::create($attrs);

        try {
            if ($request->filled('device_token') && Schema::hasColumn('users', 'device_token')) {
                $user->device_token = (string) $request->string('device_token');
            }
            if ($request->filled('language') && Schema::hasColumn('users', 'language')) {
                $user->language = (string) $request->string('language');
            }
            $user->save();
        } catch (\Throwable $e) {
            // ignore optional column errors
        }

        event(new Registered($user));

        return response()->json([
            'message' => 'User registered successfully. Please verify your email.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Login a user and return JWT token.
     */



public function login(Request $request)
{
    // ✅ 1) Validate FIRST
    $validator = Validator::make($request->all(), [
        'email'        => ['required', 'email'],
        'password'     => ['required', 'string'],
        'device_token' => ['nullable', 'string'],
        'country'      => ['required', 'string', 'max:150'],
        'city'         => ['required', 'string', 'max:150'],
        'language'     => ['nullable', 'string', 'in:en,ar'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'validation_error',
            'errors' => $validator->errors(),
        ], 422);
    }

    // ✅ 2) Auth attempt
    $credentials = $request->only(['email', 'password']);
    if (!Auth::attempt($credentials)) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /** @var \App\Models\User $user */
    $user = Auth::user();

    if (method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
        return response()->json(['error' => 'Email not verified'], 403);
    }

    $countryNameEn = trim((string) $request->input('country'));
    $cityNameEn    = trim((string) $request->input('city'));

    // if you don't have Arabic names from mobile, fallback to english
    $countryNameAr = $countryNameEn;
    $cityNameAr    = $cityNameEn;

    // ✅ 3) Get or Create Country + City + Way safely
    try {
        DB::transaction(function () use (
            $countryNameEn,
            $cityNameEn,
            $request, $user
        ) {
            $this->setCountryAndCity($user,$countryNameEn,$cityNameEn);
            // -------- Country --------


            // optional fields
            if (Schema::hasColumn('users', 'device_token') && $request->filled('device_token')) {
                $user->device_token = (string) $request->input('device_token');
            }
            if (Schema::hasColumn('users', 'language') && $request->filled('language')) {
                $user->language = (string) $request->input('language');
            }

            $user->save();
        });
    } catch (\Throwable $e) {
        // If DB ops fail, return clear error
        return response()->json([
            'error'   => 'Failed to update location data',
            'message' => $e->getMessage(),
        ], 500);
    }

    // ✅ 4) JWT token (same logic)
    $now    = new CarbonImmutable();
    $expiry = $now->addHours(24);

    $token = $this->jwtConfig->builder()
        ->issuedBy(config('app.url'))
        ->permittedFor(config('app.url'))
        ->issuedAt($now)
        ->canOnlyBeUsedAfter($now)
        ->expiresAt($expiry)
        ->relatedTo((string) $user->id)
        ->withClaim('email', $user->email)
        ->getToken(
            $this->jwtConfig->signer(),
            $this->jwtConfig->signingKey()
        );

    $user->access_token = $token->toString();
    $user->load('country');
    $user->load('city');
    return response()->json([
        'data'       => $user,
        'country'=> $cityNameEn,
        'city'=> $cityNameEn,
        'token_type' => 'Bearer',
        'expires_in' => $expiry->diffInSeconds($now),
    ], 200);
}


function setCountryAndCity($user,$countryName,$cityName){
       $country = Country::firstOrCreate(
                ['name_en' => $countryName],
                ['name_ar' => $countryName, 'is_active' => true]
            );

            if (isset($country->is_active) && !$country->is_active) {
                $country->is_active = true;
                $country->save();
            }

            // -------- City --------
            $city = City::firstOrCreate(
                ['country_id' => $country->id, 'name_en' => $cityName],
                ['name_ar' => $cityName, 'is_active' => true]
            );

            if (isset($city->is_active) && !$city->is_active) {
                $city->is_active = true;
                $city->save();
            }

            // ✅ Create way ONLY when either country/city was newly created
            if ($country->wasRecentlyCreated || $city->wasRecentlyCreated) {
                $nameEn = trim(($country->name_en ?? '') . ' - ' . ($city->name_en ?? ''));
                $nameAr = trim(($country->name_ar ?? $country->name_en ?? '') . ' - ' . ($city->name_ar ?? $city->name_en ?? ''));

                TraspartationWay::firstOrCreate(
                    [
                        'country_id' => $country->id,
                        'city_id'    => $city->id,
                    ],
                    [
                        'name_en'    => $nameEn,
                        'name_ar'    => $nameAr,
                        'days_count' => 5,
                        'is_active'  => true,
                    ]
                );
            }

            // ✅ Update user location always
            if (Schema::hasColumn('users', 'country_id')) {
                $user->country_id = $country->id;
            }
            if (Schema::hasColumn('users', 'city_id')) {
                $user->city_id = $city->id;
            }
}
    /**
     * Update language/theme settings.
     */
    public function updateSettings(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'language'        => 'nullable|string|in:en,ar',
            'theme'           => 'nullable|string|in:light,dark,system',
            'notification_on' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updated = false;

        if ($request->filled('language')) {
            $user->language = $request->string('language');
            try {
                App::setLocale($user->language);
                Session::put('locale', $user->language);
            } catch (\Throwable $e) {}
            $updated = true;
        }

        if ($request->filled('theme')) {
            $user->theme = $request->string('theme');
            $updated = true;
        }

        if ($updated) {
            $user->save();
        }

        return response()->json([
            'status'   => 'ok',
            'message'  => $updated ? 'Settings updated.' : 'Nothing to update.',
            'settings' => [
                'language' => $user->language ?? null,
                'theme'    => $user->theme ?? null,
            ],
        ], 200);
    }

    /**
     * Forgot password: send reset link (no user enumeration).
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'language' => 'nullable|string|in:en,ar',
        ]);

        if ($request->filled('language')) {
            try { App::setLocale($request->string('language')); } catch (\Throwable $e) {}
        }

        $status = Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => __($status),
            'status'  => $status,
        ], 200);
    }

    /**
     * Resend verification email for current user.
     */
    public function resendVerificationEmail(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        if (method_exists($user, 'hasVerifiedEmail') && $user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 409);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent'], 200);
    }
  public function refresh(Request $request)
{
    try {
        // 1) نقرأ التوكن القديم من الهيدر
        $oldTokenString = $request->bearerToken();

        if (!$oldTokenString) {
            return response()->json([
                'message' => 'Token not provided.',
            ], 401);
        }

        // 2) نحاول نحلّل التوكن
        try {
            $parsedToken = $this->jwtConfig->parser()->parse($oldTokenString);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Invalid token format.',
            ], 401);
        }


        // 3) نجيب user_id من claim sub (relatedTo اللي حطيتيه في login)
        $userId = $parsedToken->claims()->get('sub', null);

        if (!$userId) {
            return response()->json([
                'message' => 'Token has no subject.',
            ], 401);
        }

        /** @var \App\Models\User|null $user */
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        // 4) نبني توكن جديد لنفس المستخدم
        $now    = new CarbonImmutable();
        $expiry = $now->addHour();

        $newToken = $this->jwtConfig->builder()
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiry)
            ->relatedTo((string) $user->id)
            ->withClaim('email', $user->email)
            ->getToken(
                $this->jwtConfig->signer(),
                $this->jwtConfig->signingKey()
            );

        return response()->json([
            'access_token' => $newToken->toString(),
            'token_type'   => 'bearer',
            'expires_in'   => $expiry->diffInSeconds($now), // مثلاً ساعة
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Token is invalid, expired, or cannot be refreshed.',
        ], 401);
    }
}

    public function resendForgotPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'language' => 'nullable|string|in:en,ar',
        ]);

        if ($request->filled('language')) {
            try { App::setLocale($request->string('language')); } catch (\Throwable $e) {}
        }

        $email = strtolower(trim((string) $request->input('email')));
        $ip    = (string) $request->ip();
        $key   = "pwd_reset_cooldown:{$email}:{$ip}";

        $seconds = 60;
        if (Cache::has($key)) {
            return response()->json([
                'message' => __('passwords.sent'),
                'status'  => Password::RESET_LINK_SENT,
            ], 200);
        }

        $status = Password::sendResetLink(['email' => $email]);
        Cache::put($key, 1, $seconds);

        return response()->json([
            'message' => __($status),
            'status'  => $status,
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully'], 200);
        }

        return response()->json(['error' => __($status)], 500);
    }

    /**
     * Update user profile (+ avatar).
     *
     * Send as multipart/form-data:
     * - file:   avatar        (image)
     * - string: avatar_path   (URL or relative storage path) [optional alternative]
     */


    public function updateProfile(Request $request)
{
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $validator = Validator::make($request->all(), [
        'name'    => 'nullable|string|max:255',
        'email'   => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
        'street'  => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'phone'   => [
            'nullable','string','max:255',
            Rule::unique('users', 'phone')->ignore($user->id),
            'regex:/^\+?[0-9\s\-\(\)]{7,20}$/',
        ],
        'avatar'      => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        'avatar_path' => 'nullable|string|max:1024',
        'country' => 'nullable|string|max:1024',
        'city' => 'nullable|string|max:1024',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'validation_error',
            'errors' => $validator->errors(),
        ], 422);
    }

    $emailChanged = false;

    if ($request->filled('name'))    $user->name    = (string) $request->input('name');
    if ($request->filled('street'))  $user->street  = (string) $request->input('street');
    if ($request->filled('address')) $user->address = (string) $request->input('address');
    if ($request->filled('phone'))   $user->phone   = (string) $request->input('phone');

    if ($request->filled('email') && $request->input('email') !== $user->email) {
        $user->email = (string) $request->input('email');
        if (Schema::hasColumn('users', 'email_verified_at')) {
            $user->email_verified_at = null;
            $emailChanged = true;
        }
    }

    // ================== AVATAR ==================
    // إذا العميل بعث ملف avatar
    if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
        // خزّن في public disk
        $path = $request->file('avatar')->store('users', 'public'); // users/xxx.webp
      $user->avatar_path = asset('storage/' . $path);

        // زن "path" فقط داخل DB
    }
    // إذا العميل بعث avatar_path (رابط جاهز)
    // elseif ($request->filled('avatar_path')) {
    //     $user->avatar_path = trim((string) $request->input('avatar_path'));
    // }
    // ✅ إذا avatar null وما في avatar_path => لا تعمل شيء (تبقي الصورة القديمة)

                $this->setCountryAndCity($user,$request->country,$request->city,$request->city);

    $user->save();

    if ($emailChanged && method_exists($user, 'sendEmailVerificationNotification')) {
        try { $user->sendEmailVerificationNotification(); } catch (\Throwable $e) {}
    }

    // ================== RESPONSE URL ==================


    return response()->json([
        'message' => 'Profile updated successfully.',
        'user' => [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'street'      => $user->street,
            'address'     => $user->address,
            'phone'       => $user->phone,
            'avatar_path' => $user->avatar_path, // path أو url
      // url جاهز للعرض
        ],
    ], 200);
}




    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password'         => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'validation_error',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Hash::check($request->string('current_password'), $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 403);
        }

        $user->password = Hash::make($request->string('password'));
        $user->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }
}
