<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends User
{
    protected $table = 'vendors';
    protected $primaryKey = 'vendor_id';
    public $incrementing = false;
    protected $fillable = ['company_name', 'company_address', 'category_id'];

    public function category()
    {
        return $this->belongsTo(VendorCategory::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }
}
