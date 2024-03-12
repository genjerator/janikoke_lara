<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'genjerator',
            'email' => 'e.medjesi@gmail.com',
            'password' => Hash::make("Evgenije"),
        ]);
    }
}
