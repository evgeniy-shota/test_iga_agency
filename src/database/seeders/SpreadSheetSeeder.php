<?php

namespace Database\Seeders;

use App\Models\SpreadSheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpreadSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SpreadSheet::factory()->count(1)->create();
    }
}
