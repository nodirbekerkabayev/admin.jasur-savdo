<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Debt;
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
            'email' => 'admin',
            'password' => bcrypt('1234')
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
        for ($i = 0; $i < 10; $i++) {
            Debt::create([
                'client_id' => $i+1,
                'amount' => $i . '00000',
                'status' => $i % 2 === 0 ? 'oldi' : 'berdi',
                'recorded_by' => ['jasur', 'nodira', 'hilola'][array_rand(['jasur', 'nodira', 'hilola'])],
                'is_deleted' => $i % 2 === 0 ? true : false,
            ]);
        }
    }
}
