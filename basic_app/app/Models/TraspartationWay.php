<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TraspartationWay extends Model
{
    protected $table = 'transpartation_way';

    protected $fillable = [
        'name_en',
        'name_ar',
        'country_id',
        'city_id',
        'is_active',
        'user_id',
        'days_count',
        'type_id',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ type relation
    public function type(): BelongsTo
    {
        return $this->belongsTo(TranspartationType::class, 'type_id');
    }
}
