<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }
}
