<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryHistory extends Model
{

    protected $table = 'country_history';
    protected $fillable = [ 'name_en', 'name_ar', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
