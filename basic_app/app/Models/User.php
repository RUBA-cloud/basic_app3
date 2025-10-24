<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Auth\MustVerifyEmail;

// ✅ Correct casing & classes for lcobucci/jwt v4
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'avatar_path','address','street','notification_on'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    /** ----------------- Relations ----------------- */

    // 👇 Many-to-many (pivot table: permission_user by default)
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    // Get only employees
    public function scopeEmployees($q)
    {
        return $q->where('role', 'employee');
    }

    // (optional) Most recent module snapshot
    public function modulesHistory()
    {
        return $this->hasOne(Module::class, 'user_id')->latestOfMany();
    }

    // Device tokens (for FCM)
    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function routeNotificationForFcm(): array
    {
        return $this->deviceTokens()->pluck('token')->all();
    }

    /** ----------------- Accessors ----------------- */

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
                return Storage::url($this->avatar_path);
            }
            return asset('images/avatar-placeholder.png');
        });
    }

    /** ----------------- JWT helper (optional) ----------------- */

    public function createJwtToken(): string
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(env('JWT_SECRET', 'change-me'))
        );

        $now    = new \DateTimeImmutable();
        $expiry = $now->modify('+1 hour');

        $token = $config->builder()
            ->issuedBy(config('app.url', env('APP_URL')))   // iss
            ->permittedFor(config('app.url', env('APP_URL'))) // aud
            ->issuedAt($now)                                 // iat
            ->expiresAt($expiry)                             // exp
            ->withClaim('uid', $this->id)                    // custom claim
            ->getToken($config->signer(), $config->signingKey());

        return $token->toString();
    }
}
