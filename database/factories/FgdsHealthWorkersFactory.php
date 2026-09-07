<?php

namespace Database\Factories;

use App\Models\FgdsHealthWorkers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FgdsHealthWorkers>
 */
class FgdsHealthWorkersFactory extends Factory
{
    protected $model = FgdsHealthWorkers::class;

    public function definition(): array
    {
        return [
            'date' => now()->subDays($this->faker->numberBetween(1, 90)),
            'hfs' => 'Govt Dispensary '.$this->faker->streetName(),
            'address' => $this->faker->streetAddress(),
            // A raw, unconsolidated spelling — the same shape real submissions have.
            'uc' => 'Gujro Zone C',
            'fix_site' => 'Govt Dispensary Bilal Colony',
            'group_type' => 'LHW',
            'facilitator_tkf' => $this->faker->name(),
            'participants_males' => 0,
            'participants_females' => 0,
            'latitude' => 24.9056,
            'longitude' => 67.0822,
        ];
    }
}
