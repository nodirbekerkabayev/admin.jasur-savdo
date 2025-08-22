<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['optom_id', 'created_by', 'total_sum'];

    public function optom()
    {
        return $this->belongsTo(Optom::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
