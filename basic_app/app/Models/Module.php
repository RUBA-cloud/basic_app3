<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';

    protected $fillable = [
        'company_dashboard_module',
        'company_info_module',
        'company_branch_module',
        'company_category_module',
        'company_type_module',
        'company_size_module',
        'company_offers_type_module',
        'company_offers_module',
        'product_module',
        'employee_module',
        'order_module',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'company_dashboard_module'   => 'boolean',
        'company_info_module'        => 'boolean',
        'company_branch_module'      => 'boolean',
        'company_category_module'    => 'boolean',
        'company_type_module'        => 'boolean',
        'company_size_module'        => 'boolean',
        'company_offers_type_module' => 'boolean',
        'company_offers_module'      => 'boolean',
        'product_module'             => 'boolean',
        'employee_module'            => 'boolean',
        'order_module'               => 'boolean',
        'is_active'                  => 'boolean',
    ];

    // Relation with User (if needed)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
