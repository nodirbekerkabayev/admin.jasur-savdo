<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    protected $fillable = [
        'name',
        'supervisor',
        's_phone',
        'agent',
        'a_phone',
        'currier',
        'c_phone',
        'humo',
        'uzcard',
        'day',
        'debt',
        'is_deleted',
    ];

    protected $casts = [
        'humo' => 'boolean',
        'uzcard' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function firmDebts()
    {
        return $this->hasMany(FirmDebt::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
