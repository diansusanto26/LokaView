<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinTopUp extends Model
{
    protected $fillable = [
        'code',
        'user_id',
        'coin_package_id',
        'coin_amount',
        'amount',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(CoinPackage::class, 'coin_package_id');
    }
}
