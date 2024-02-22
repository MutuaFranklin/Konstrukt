<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorCategory extends Model
{
    protected $table = 'vendor_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'category_id');
    }
}
