<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = ['name', 'description', 'price', 'vendor_id', 'stock_quantity'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
