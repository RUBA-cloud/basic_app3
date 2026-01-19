<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{

    protected $table = 'country';
    protected $fillable = ['name_en', 'name_ar', 'is_active'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cities()  {
        return $this->belongsTo(City::class);
    }
}
