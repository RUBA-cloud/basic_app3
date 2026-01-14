<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranspartationType extends Model
{
    //
    protected $table = "transpartation_types";

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
