<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = [
        'vehiclePlate', 'entryDateTime', 'exitDateTime', 'priceType', 'price', 'userId'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
}