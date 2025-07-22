<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'name',
        'contact',
        'date',
        'location',
        'notes',
        'status',
        'payment_status',
        'midtrans_order_id',
        'total_price',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
