<?php

namespace Database\Seeders;

use App\Models\Row;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Row::factory()->count(100)->create(['user_id' => User::inRandomOrder()->first()]);
    }
}
