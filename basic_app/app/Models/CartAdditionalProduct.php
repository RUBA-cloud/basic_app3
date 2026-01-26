<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartAdditionalProduct extends Model
{
    //
    protected $table = "cart_additional_product";
    protected $fillable = [
        "cart_id",
        "product_id",
        "additional_id",

    ];
 public function cart(){

    return $this->belongsTo(Cart::class,"cart_id","id");
 }
    public function product() {
        return $this->belongsTo(Product::class,"product_id","id");
    }
    public function additioanls() {
         return $this->belongsTo(Additonal::class,"additional_id","id");
}
}
