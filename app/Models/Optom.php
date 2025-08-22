<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Optom extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'created_by'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
