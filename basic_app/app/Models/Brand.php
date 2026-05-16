<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    // ── Fillable ───────────────────────────────────────────────────────────────
    protected $fillable = [
        'name_en',
        'name_ar',
        'image',
        'is_active',
        'is_top',
        'company_id',
        'user_id',
    ];

    // ── Casts ──────────────────────────────────────────────────────────────────
    protected $casts = [
        'is_active'  => 'boolean',
        'is_top'     => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // ── Appends ────────────────────────────────────────────────────────────────
    protected $appends = ['image_url'];

    // ── Accessors ──────────────────────────────────────────────────────────────

    /**
     * Full public URL for the brand image.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? Storage::url($this->image)
            : null;
    }

    // ── Relations ──────────────────────────────────────────────────────────────

    /**
     * The company this brand belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * The user (admin) who created / last updated this brand.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Only active brands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Only top-ranked brands.
     */
    public function scopeTop($query)
    {
        return $query->where('is_top', true);
    }

    /**
     * Brands belonging to a specific company.
     */
    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}