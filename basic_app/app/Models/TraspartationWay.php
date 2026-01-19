<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TraspartationWay extends Model
{
    protected $table = "traspartation_ways";
    protected $fillable = ["name_en", "name_ar",'country_id', 'city_id', 'is_active', 'user_id,', 'days_count','type_id'];


    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function city(){
        return $this->belongsTo(City::class);
    }
    public function transpartatiom(){
        return $this->belongsTo(TranspartationType::class);
    }
}
