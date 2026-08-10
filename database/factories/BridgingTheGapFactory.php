<?php

namespace Database\Factories;

use App\Models\BridgingTheGap;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BridgingTheGap>
 */
class BridgingTheGapFactory extends Factory
{
    protected $model = BridgingTheGap::class;

    public function definition(): array
    {
        return [
            'date' => now()->subDays($this->faker->numberBetween(1, 90)),
            'venue' => $this->faker->streetName().' Community Hall',
            'district' => 'Karachi',
            'uc' => 'Gujro Zone C',
            'fix_site' => 'BHU Gujro',
            'participants_males' => 0,
            'participants_females' => 0,
            'latitude' => 24.9056,
            'longitude' => 67.0822,
        ];
    }
}
