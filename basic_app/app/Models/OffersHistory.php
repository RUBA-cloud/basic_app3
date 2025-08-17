<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OffersHistory extends Model
{
    //
    protected $table = 'offers_history'; // Ensure the table name is correct
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
        'offer_percentage', // Assuming you have an offer percentage field
    ];
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'type_id' => 'integer', // Assuming type_id is an integer
        'user_id' => 'integer',
        'colors' => 'array', // Assuming colors is stored as JSON
        'offer_percentage' => 'float', // Assuming offer_percentage is a float
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');         }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');}
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');}
}
