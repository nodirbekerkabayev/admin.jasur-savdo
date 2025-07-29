<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'amount',
        'day',
        'status',
        'image',
        'summa',
    ];

    protected $casts = [
        'day' => 'date',
        'summa' => 'integer',
    ];

    public function pays()
    {
        return $this->hasMany(WorkerPay::class);
    }
}
