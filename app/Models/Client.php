<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'info',
        'phone',
        'image',
        'debt',
        'recorded_by',
        'is_deleted',
    ];
    protected $casts = [
        'is_deleted' => 'boolean',
    ];
}
