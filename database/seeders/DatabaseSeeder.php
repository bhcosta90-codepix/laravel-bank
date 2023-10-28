<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Account::factory()
            ->hasPix(1, ['kind' => 'email', 'key' => 'account1@account.com'])
            ->create(['id' => "65b1e7a5-9f03-4e5f-8a50-9aebb21013dd"]);

        Account::factory()
            ->hasPix(1, ['kind' => 'email', 'key' => 'account2@account.com'])
            ->create(['id' => "3966a0bd-e915-4d55-b3d7-83062799eecf"]);
    }
}
