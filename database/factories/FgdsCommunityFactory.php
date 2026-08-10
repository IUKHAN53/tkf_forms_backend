<?php

namespace Database\Factories;

use App\Models\FgdsCommunity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FgdsCommunity>
 */
class FgdsCommunityFactory extends Factory
{
    protected $model = FgdsCommunity::class;

    public function definition(): array
    {
        return [
            'date' => now()->subDays($this->faker->numberBetween(1, 90)),
            'venue' => $this->faker->streetName().' Community Hall',
            'district' => 'Karachi',
            // A raw, unconsolidated spelling — the same shape real submissions have.
            'uc' => 'Gujro Zone C',
            'fix_site' => 'BHU Gujro',
            'outreach' => 'Outreach A',
            'community' => ['Mohalla One', 'Mohalla Two'],
            'facilitator_tkf' => $this->faker->name(),
            'participants_males' => 0,
            'participants_females' => 0,
            'latitude' => 24.9056,
            'longitude' => 67.0822,
        ];
    }
}
