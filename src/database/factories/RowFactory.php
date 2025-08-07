<?php

namespace Database\Factories;

use App\Enums\SpreadSheetRowStatus;
use App\Models\SpreadSheet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Row>
 */
class RowFactory extends Factory
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
            $columns['Col ' . fake()->text(5)] = fake()->word();
        }

        $reservedCount = fake()->numberBetween(0, 999);

        return [
            'spread_sheets_id' => SpreadSheet::inRandomOrder()->first() ?? SpreadSheet::factory(),
            'user_id' => User::inRandomOrder()->first(),
            'status' => fake()->randomElement(SpreadSheetRowStatus::getValues()),
            'name' => fake()->word(),
            'reserved_count' => fake()->numberBetween(0, 999),
            'total_count' => fake()->numberBetween($reservedCount, 1999),
        ];
    }
}
