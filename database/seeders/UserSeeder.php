<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Cassio',
            'email' => 'cassio@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('cassio1234')
        ])->assignRole('admin');
    }
}
