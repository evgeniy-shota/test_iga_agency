<?php

namespace Database\Seeders;

use App\Models\SpreadSheet;
use App\Models\SpreadSheetData;
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
        $listNumbers = fake()->numberBetween(1, 4);
        $columnsNumbers = fake()->numberBetween(3, 7);

        $this->call([
            SpreadSheetSeeder::class,
            RowSeeder::class,
        ]);
    }
}
