<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'coin_balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
