<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends User
{
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    public $incrementing = false;
    protected $fillable = ['shipping_address', 'loyalty_points'];


    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
}
