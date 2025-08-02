<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpreadSheet>
 */
class SpreadSheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $size = 3;

        for ($i = 0; $i < $size; $i++) {
            $sheets['list ' . fake()->text(5)] = fake()->numberBetween(10000, 100000);
        }

        return [
            'user_id' => User::factory(),
            'url' => 'https://docs.google.com/spreadsheets/d/1KEwADgmA0v2MdRoL1UHMx7B1MQkfejVUY3dzD0DP2xQ/edit?gid=35832068#gid=35832068',
            'sheets' => json_encode($sheets),
            'current_sheet' => array_rand($sheets),
        ];
    }
}
