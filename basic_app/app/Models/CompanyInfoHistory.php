<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfoHistory extends Model
{
    use HasFactory;

    // FIX: Notifiable removed — it belongs on User, not on a history log model.
    // Notifiable adds notification channels (mail, database, etc.) which a
    // history record never needs and adds unnecessary overhead.

    protected $table = 'company_info_history';

    protected $fillable = [
        'image',
        'name_en',
        'name_ar',
        'phone',
        'email',
        'address_en',
        'address_ar',
        'location',
        'about_us_en',
        'about_us_ar',
        'mission_en',
        'mission_ar',
        'vision_en',
        'vision_ar',
        'country_id',
        'city_id',
        'main_color',
        'sub_color',
        'text_color',
        'button_color',
        'button_text_color',
        'icon_color',
        'text_filed_color',
        'card_color',
        'label_color',
        'hint_color',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* ── Relationships ──────────────────────────────────────── */

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}