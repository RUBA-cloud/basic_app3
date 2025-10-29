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
     * Map feature flags (columns on `modules` table) → permission slugs (`permissions.module_name`).
     */
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

    /* ============================================================
     | Relations
     ============================================================ */

    /**
     * MANY-TO-MANY permissions via pivot `permission_user`.
     * Expected `permissions` columns: module_name, can_add, can_edit, can_delete, can_view_history, is_active, ...
     */
    public function permissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class, 'permission_user')->withTimestamps();
    }

    /**
     * Most recent modules snapshot for this user.
     * Table `modules` must have per-module boolean flags and `is_active`.
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
     * Resolve the current Module row for the user (robust to relation not being preloaded).
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
     * True if the module flag is enabled AND user has at least one active ability on that module.
     * (This is what drives the menu visibility.)
     */
    public function canUseModule(string $featureKey): bool
    {

        return $this->hasAnyPermissionForFeature($featureKey);
    }

    /**
     * True if there's an active permission row for the mapped module slug
     * AND at least one of: can_add | can_edit | can_delete | can_view_history.
     */
    public function hasAnyPermissionForFeature(string $featureKey): bool
    {
        $moduleName = self::MODULE_FLAG_TO_NAME[$featureKey] ?? null;

        if (!$moduleName) {
            return false;
        }

        $perm = $this->permissionFor($moduleName);
        if (!$perm) {
            return false;
        }

        return (bool)(
            ($perm->can_add ?? false) ||
            ($perm->can_edit ?? false) ||
            ($perm->can_delete ?? false) ||
            ($perm->can_view_history ?? false)
        );
    }

    /**
     * Return [feature_flag => module_slug, ...] for modules the user can actually use.
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
     * Ability check for a single ability: can_add | can_edit | can_delete | can_view_history.
     */
    public function hasPermission(string $moduleName, string $ability): bool
    {
        if (!in_array($ability, ['can_add', 'can_edit', 'can_delete', 'can_view_history'], true)) {
            return false;
        }

        // In-memory check if eager-loaded
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

    /**
     * Accessor: $user->avatar_url
     */
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
