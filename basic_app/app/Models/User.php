<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_path',
        'address',
        'street',
        'notification_on',
        'country_id',
        'city_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_on'   => 'boolean',
    ];

    public const MODULE_FLAG_TO_NAME = [
        'company_dashboard_module'   => 'company_dashboard_module',
        'company_info_module'        => 'company_info_module',
        'company_branch_module'      => 'company_branch_module',
        'company_category_module'    => 'company_category_module',
        'company_type_module'        => 'company_type_module',
        'company_size_module'        => 'company_size_module',
        'company_offers_type_module' => 'company_offers_type_module',
        'company_offers_module'      => 'company_offers_module',
        'product_module'             => 'product_module',
        'employee_module'            => 'employee_module',
        'order_module'               => 'order_module',
        'order_status_module'        => 'order_status_module',
        'region_module'              => 'region_module',
        'company_delivery_module'    => 'company_delivery_module',
        'payment_module'             => 'payment_module',
    ];

    // =========================
    // Relations
    // =========================

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user')->withTimestamps();
    }

    public function modulesHistory()
    {
        return $this->hasOne(Module::class, 'user_id')->latestOfMany();
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class, 'user_id');
    }

    // ✅ Country/City relations (fixed)
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    // =========================
    // Main admin logic
    // =========================

    public function isMainAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin';
    }

    public function hasModuleFeature(string $featureKey): bool
    {
        return $this->isMainAdmin() ? true : $this->canUseModule($featureKey);
    }

    public function canUseModule(string $featureKey): bool
    {
        return $this->isMainAdmin() ? true : $this->hasAnyPermissionForFeature($featureKey);
    }

    public function hasPermission(string $moduleName, string $ability): bool
    {
        if ($this->isMainAdmin()) return true;

        if (!in_array($ability, ['can_add', 'can_edit', 'can_delete', 'can_view_history'], true)) {
            return false;
        }

        if ($this->relationLoaded('permissions')) {
            return $this->permissions
                ->where('module_name', $moduleName)
                ->where('is_active', true)
                ->contains(fn($p) => (bool) ($p->{$ability} ?? false));
        }

        return $this->permissions()
            ->where('permissions.module_name', $moduleName)
            ->where('permissions.is_active', true)
            ->where("permissions.$ability", true)
            ->exists();
    }

    public function availableModules(): array
    {
        if ($this->isMainAdmin()) return self::MODULE_FLAG_TO_NAME;

        $mods = [];
        foreach (self::MODULE_FLAG_TO_NAME as $flag => $slug) {
            if ($this->canUseModule($flag)) $mods[$flag] = $slug;
        }
        return $mods;
    }

    public function moduleRow(): ?Module
    {
        if ($this->relationLoaded('modulesHistory')) {
            $rel = $this->getRelation('modulesHistory');
            if ($rel instanceof Module) return $rel;
        }

        try {
            $rel = $this->modulesHistory()->first();
            if ($rel) return $rel;
        } catch (\Throwable $e) {}

        try {
            return Module::where('user_id', $this->id)->orderByDesc('id')->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function hasAnyPermissionForFeature(string $featureKey): bool
    {
        $moduleName = self::MODULE_FLAG_TO_NAME[$featureKey] ?? null;
        if (!$moduleName) return false;

        $perm = $this->permissionFor($moduleName);
        if (!$perm) return false;

        return (bool)(
            ($perm->can_add ?? false) ||
            ($perm->can_edit ?? false) ||
            ($perm->can_delete ?? false) ||
            ($perm->can_view_history ?? false)
        );
    }

    public function permissionFor(string $moduleName): ?Permission
    {
        if ($this->isMainAdmin()) {
            return new Permission([
                'module_name'      => $moduleName,
                'can_add'          => true,
                'can_edit'         => true,
                'can_delete'       => true,
                'can_view_history' => true,
                'is_active'        => true,
            ]);
        }

        if ($this->relationLoaded('permissions')) {
            return $this->permissions->first(fn($p) => $p->module_name === $moduleName && (bool) $p->is_active);
        }

        return $this->permissions()
            ->where('permissions.module_name', $moduleName)
            ->where('permissions.is_active', true)
            ->first();
    }

    // =========================
    // Scopes / Notifications
    // =========================

    public function scopeEmployees($q)
    {
        return $q->where('role', 'employee');
    }

    public function routeNotificationForFcm(): array
    {
        return $this->deviceTokens()->pluck('token')->all();
    }

    // =========================
    // Accessors
    // =========================

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
                return Storage::url($this->avatar_path);
            }
            return asset('images/avatar-placeholder.png');
        });
    }

    // =========================
    // Other relations
    // =========================

    public function faviorates()
    {
        return $this->hasMany(FaviorateModel::class, 'user_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id');
    }
}
