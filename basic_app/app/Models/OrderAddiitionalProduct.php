<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddiitionalProduct extends Model
{
    protected $table = "order_additional_model";
    protected $fillable = ['product_id','order_id','additional_id'];


    public function product(){
        return $this->belongsTo(Product::class,'product_id','id');
    }
    public function additionalProduct(){
        return $this->belongsTo(Additonal::class,'additional_id','id');
    }
}
