<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
class ProductHistory extends Model
{
    //
    use HasFactory, Notifiable;
    protected $table = 'products_history'; // Ensure the table name is correct
   protected $fillable = [
        'name_en',
        'name_ar',
        'description_en',
        'description_ar',
        'price',
        'is_active',
        'user_id',
        'category_id',
        'type_id', // Foreign key to types table
        'colors', // Assuming you have a colors field, can be JSON or text
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'type_id' => 'integer', // Assuming type_id is an integer
        'user_id' => 'integer',
        'sizes' => 'array', // Assuming sizes is stored as JSON
        'additional' => 'array', // Assuming additional is stored as JSON
        'name_en' => 'string',
        'name_ar' => 'string',
        'description_en' => 'string',
        'category_id' => 'integer',
        'colors' => 'array', // Assuming colors is stored as JSON
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');    }
    public function images()
    {
        return $this->hasMany(ProductImageHistory::class, 'product_id');
    }

    public function sizes(){
        return $this->hasMany(ProductSizeHistory::class, 'product_id');
    }
    public function additionals()
    {
        return $this->hasMany(ProductAdditionalHistory::class, 'product_history_id');
    }
}
