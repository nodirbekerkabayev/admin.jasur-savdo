<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => "Jasur Sultonov",
            'email' => 'jasursavdo',
            'password' => bcrypt('12021985')
        ]);
        for ($i = 0; $i < 10; $i++) {
            Client::create([
                'name' => "Client name" . $i,
                'info' => "Client info" . $i,
                'phone' => "+99899765432" . $i,
                'image' => "clients" . $i . ".jpg",
                'debt' => $i . '00000',
                'recorded_by' => ['jasur', 'nodira', 'hilola'][array_rand(['jasur', 'nodira', 'hilola'])],
                'is_deleted' => $i % 2 === 0 ? true : false,
            ]);
        }
    }
}
