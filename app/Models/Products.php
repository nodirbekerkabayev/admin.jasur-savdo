<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'karobkadagi_soni',
        'necha_karobka_kelgani',
        'kelgan_narxi_dona',
        'kelgan_narxi_blok',
        'sotish_narxi_dona',
        'sotish_narxi_blok',
        'sotish_narxi_optom_dona',
        'sotish_narxi_optom_blok',
        'sotish_narxi_toyga_dona',
        'sotish_narxi_toyga_blok',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
