<?php

namespace App\Console\Commands;

use App\Models\Worker;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateWorkerSumma extends Command
{
    protected $signature = 'workers:update-summa';
    protected $description = 'Update workers summa based on working days';

    public function handle()
    {
        $workers = Worker::where('status', 'ishlayabdi')->get();

        foreach ($workers as $worker) {
            $startDate = Carbon::parse($worker->day);
            $today = Carbon::today('Asia/Tashkent');
            $daysWorked = $startDate->diffInDays($today) + 1; // Including start day
            $worker->summa = $daysWorked * $worker->amount;
            $worker->save();
        }

        $this->info('Worker summa updated successfully.');
    }
}
