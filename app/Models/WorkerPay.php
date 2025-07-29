<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerPay extends Model
{
    protected $fillable = [
        'worker_id',
        'amount',
        'status',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
