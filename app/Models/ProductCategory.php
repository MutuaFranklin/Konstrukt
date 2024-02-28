<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $table = 'product_categories';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];

    public function Products()
    {
        return $this->hasMany(Product::class, 'product_id');
    }
}
