<?php

namespace App\Console\Commands;

use App\Models\Worker;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateWorkerSumma extends Command
{
    protected $signature = 'workers:update-summa';
    protected $description = 'Update workers summa based on working days';

    public function handle()
    {
        $workers = Worker::where('status', 'ishlayabdi')->get();
        $now = Carbon::now('Asia/Tashkent');
        $isAfter11AM = $now->hour >= 11;

        foreach ($workers as $worker) {
            $startDate = Carbon::parse($worker->day);
            $today = Carbon::today('Asia/Tashkent');
            $daysWorked = $startDate->diffInDays($today) + ($isAfter11AM ? 1 : 0); // Include current day if after 11 AM
            $newSumma = $daysWorked * $worker->amount;

            $worker->summa = $newSumma;
            $worker->save();
            Log::info("Worker ID: {$worker->id}, Days: {$daysWorked}, Summa: {$worker->summa}");
        }

        $this->info('Worker summa updated successfully.');
    }
}
