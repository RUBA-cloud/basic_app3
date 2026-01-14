<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranspartationTypeHistory extends Model
{
    //
     protected $table = "transportation_type_history";

    protected $fillable = [
        'name_en',
        'name_ar',
        'is_active',
        'user_id',
    ];

    public $timestamps = false;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

