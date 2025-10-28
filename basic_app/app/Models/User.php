<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_path',
        'address',
        'street',
        'notification_on',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'notification_on'   => 'boolean',
    ];

    /**
     * Map module feature flags (in `modules` table) to permission slugs
     * (values stored in `permissions.module_name`).
     */
    public const MODULE_FLAG_TO_NAME = [
        'company_dashboard_module'   => 'dashboard',
        'company_info_module'        => 'company_info',
        'company_branch_module'      => 'branch',
        'company_category_module'    => 'category',
        'company_type_module'        => 'type',
        'company_size_module'        => 'size',
        'company_offers_type_module' => 'offers_type',
        'company_offers_module'      => 'offers',
        'product_module'             => 'product',
        'employee_module'            => 'employees',
        'order_module'               => 'order',
        'order_status_module'        => 'order_status',
        'region_module'              => 'regions',
        'company_delivery_module'    => 'company_delivery',
        'payment_module'             => 'payment',
    ];

    /* ============================================================
     | Relations
     ============================================================ */

    /**
     * MANY-TO-MANY permissions via pivot `permission_user`.
     * Columns expected on `permissions`: module_name, can_add, can_edit, can_delete, can_view_history, is_active, ...
     */
    public function permissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class, 'permission_user')
            ->withTimestamps();
    }

    /**
     * Most recent modules snapshot for this user.
     * Table: `modules` (has feature flags like product_module, ...).
     */
    public function modulesHistory()
    {
        return $this->hasOne(\App\Models\Module::class, 'user_id')->latestOfMany();
    }

    /**
     * Device tokens (for FCM).
     */
    public function deviceTokens()
    {
        return $this->hasMany(\App\Models\DeviceToken::class, 'user_id');
    }

    /* ============================================================
     | Module helpers (for EnsureModuleEnabled)
     ============================================================ */

    /**
     * Robustly resolve the current Module row for the user.
     * Works even if relation not preloaded.
     */
    public function moduleRow(): ?\App\Models\Module
    {
        // 1) Already-loaded relation?
        if ($this->relationLoaded('modulesHistory')) {
            $rel = $this->getRelation('modulesHistory');
            if ($rel instanceof \App\Models\Module) {
                return $rel;
            }
        }

        // 2) Try relation query
        try {
            $rel = $this->modulesHistory()->first();
            if ($rel) return $rel;
        } catch (\Throwable $e) {
            // ignore and fallback
        }

        // 3) Fallback: latest row by user_id
        try {
            return \App\Models\Module::where('user_id', $this->id)
                ->orderByDesc('id')
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Check a feature flag (e.g., 'product_module') for this user.
     */
    public function hasModuleFeature(string $featureKey): bool
    {
        $m = $this->moduleRow();
        return $m && $m->is_active && (bool) ($m->{$featureKey} ?? false);
    }

    /**
     * Full gate for menus/visibility: module flag + at least one active permission row.
     */
    public function canUseModule(string $featureKey): bool
    {
        $moduleName = self::MODULE_FLAG_TO_NAME[$featureKey] ?? null;
        if (!$moduleName) return false;

        if (!$this->hasModuleFeature($featureKey)) return false;

        return (bool) $this->permissionFor($moduleName);
    }

    /**
     * Return an array of modules the user can use: [feature_flag => module_slug, ...]
     */
    public function availableModules(): array
    {
        $mods = [];
        foreach (self::MODULE_FLAG_TO_NAME as $flag => $slug) {
            if ($this->canUseModule($flag)) {
                $mods[$flag] = $slug;
            }
        }
        return $mods;
    }

    /* ============================================================
     | Permission helpers (for EnsurePermission)
     ============================================================ */

    /**
     * Get the first **active** permission row for a module slug (e.g., 'product', 'order').
     */
    public function permissionFor(string $moduleName): ?\App\Models\Permission
    {
        // Prefer loaded permissions if available
        if ($this->relationLoaded('permissions')) {
            return $this->permissions->first(function ($p) use ($moduleName) {
                return $p->module_name === $moduleName && (bool) $p->is_active;
            });
        }

        // Otherwise query via pivot
        return $this->permissions()
            ->where('permissions.module_name', $moduleName)
            ->where('permissions.is_active', true)
            ->first();
    }

    /**
     * Ability check for: can_add | can_edit | can_delete | can_view_history
     */
    public function hasPermission(string $moduleName, string $ability): bool
    {
        if (!in_array($ability, ['can_add', 'can_edit', 'can_delete', 'can_view_history'], true)) {
            return false;
        }

        // Check in-memory if eager-loaded
        if ($this->relationLoaded('permissions')) {
            return $this->permissions
                ->where('module_name', $moduleName)
                ->where('is_active', true)
                ->contains(function (\App\Models\Permission $p) use ($ability) {
                    return (bool) ($p->{$ability} ?? false);
                });
        }

        // Efficient DB check
        return $this->permissions()
            ->where('permissions.module_name', $moduleName)
            ->where('permissions.is_active', true)
            ->where("permissions.$ability", true)
            ->exists();
    }

    /* ============================================================
     | Scopes / Notifications / Accessors
     ============================================================ */

    public function scopeEmployees($q)
    {
        return $q->where('role', 'employee');
    }

    public function routeNotificationForFcm(): array
    {
        return $this->deviceTokens()->pluck('token')->all();
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->avatar_path && Storage::disk('public')->exists($this->avatar_path)) {
                return Storage::url($this->avatar_path);
            }
            return asset('images/avatar-placeholder.png');
        });
    }
}
