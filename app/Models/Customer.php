<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends User
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = ['shipping_address', 'loyalty_points'];


    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
