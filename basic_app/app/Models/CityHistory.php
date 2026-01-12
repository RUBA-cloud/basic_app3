<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CityHistory extends Model
{
    protected $table = "cities";
    protected $fillable = [
        'name_en',
        'name_ar',
        'country_id',
        'is_active',
        'user_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
