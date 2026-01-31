<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    //
    protected $table = 'carts';
    protected $fillable = ['user_id', 'product_id', 'quantity', 'size_id', 'color'  ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function cartAdditional(){
        return $this->hasMany(\App\Models\CartAdditionalProduct::class, 'cart_id', 'id')
        ->with([
            'product:id,name_en,name_ar,price',
            'additioanls:id,name_en,name_ar,price',
        ]);

    }


}
