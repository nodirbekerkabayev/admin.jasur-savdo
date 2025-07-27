<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Debt extends Model
{
    protected $fillable = [
        'client_id',
        'amount',
        'status',
        'recorded_by',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    protected static function booted()
    {
        static::created(function ($debt) {
            $debt->updateClientDebt();
        });

        static::updated(function ($debt) {
            $debt->updateClientDebt();
        });
    }

    public function updateClientDebt()
    {
        $client = $this->client;
        if ($client) {
            $totalDebt = $client->debts()
                ->where('is_deleted', false)
                ->sum(DB::raw("CASE WHEN status = 'oldi' THEN amount ELSE -amount END"));

            $client->update(['debt' => $totalDebt]);
        }
    }
}
