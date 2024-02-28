<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends User
{
    protected $table = 'vendors';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = ['company_name', 'company_address'];


    public function products()
    {
        return $this->hasMany(Product::class, 'vendor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
