<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Modules\Orders\Notifications\OrderStatusChanged;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar_path',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
  public function modulesHistory()
    {
        return $this->hasOne(Module::class, 'user_id')->latestOfMany();
    }
    // === Scopes ===
    public function scopeEmployees($q) { return $q->where('role', 'employee'); }

    // === Relations ===
    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

public function hasVerifiedEmail():  bool
{
    return !is_null($this->email_verified_at);

}

public function markEmailAsVerified()
{
    $this->email_verified_at = now();
    $this->save();
}
    // === Accessors ===
    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
                return Storage::url($this->avatar_path);
            }
            return asset('images/avatar-placeholder.png'); // add a placeholder if you want
        });
    }
    // app/Models/User.php
public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by'); // make sure column exists
}
  public function deviceTokens(): HasMany
  {
    return $this->hasMany(DeviceToken::class);
    }
    // For the notification channel to know where to send:
    public function routeNotificationForFcm(): array
    {
        return $this->deviceTokens()->pluck('token')->all();
    }
        // If not already defined
    // Users who can handle Orders module


}
