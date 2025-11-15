<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CompanyBranch;
class Category extends Model
{
    //
protected $table = 'categories';
protected $fillable=['name_en','name_ar','is_active','image'];

 // Ensure the table name is correct

public function branches()
{
return $this->belongsToMany(CompanyBranch::class, 'category_branch', 'category_id', 'branch_id');
}
    public function user(){return $this->belongsTo(User::class, 'user_id');}
    public function categoryHistory()
    {
        return $this->hasMany(CategoryHistory::class, 'category_id');
    }
// app/Models/Category.php
public function products()
{
    // Eager-load the nested relations on Product
    return $this->hasMany(\App\Models\Product::class, 'category_id')
        ->with(['images', 'sizes', 'additionals','category','type']);// <- adjust names to your Product relations
}


}
