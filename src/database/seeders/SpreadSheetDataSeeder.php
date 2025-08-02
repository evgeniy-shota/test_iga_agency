<?php

namespace Database\Seeders;

use App\Models\SpreadSheetData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpreadSheetDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SpreadSheetData::factory()->count(100)->create();
    }
}
