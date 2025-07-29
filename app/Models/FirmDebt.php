<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FirmDebt extends Model
{
    protected $fillable = [
        'firm_id',
        'amount',
        'status',
        'recorded_by',
    ];

    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    protected static function booted()
    {
        static::created(function ($firmDebt) {
            $firmDebt->updateFirmDebt();
        });

        static::updated(function ($firmDebt) {
            $firmDebt->updateFirmDebt();
        });
    }

    public function updateFirmDebt()
    {
        $firm = $this->firm;
        if ($firm) {
            $totalDebt = $firm->firmDebts()
                ->sum(DB::raw("CASE WHEN status = 'oldi' THEN amount ELSE -amount END"));

            $firm->update(['debt' => $totalDebt]);
        }
    }
}
