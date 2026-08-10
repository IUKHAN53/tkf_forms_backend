<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\DashboardController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * UC names are typed by hand in the field and arrive in many spellings. The
 * consolidation map is what makes every report agree on which UC a record
 * belongs to, so its edge cases are worth pinning down.
 */
class UcConsolidationTest extends TestCase
{
    public static function variantProvider(): array
    {
        return [
            'plain name' => ['Muslimabad', 'Muslimabad'],
            'dash suffix' => ['Muslimabad-2', 'Muslimabad'],
            'space suffix' => ['Muslimabad 2', 'Muslimabad'],
            'zero padded suffix' => ['Muslimabad-02', 'Muslimabad'],
            'misspelling' => ['Muzafarabad', 'Muzafrabad'],
            'misspelling + suffix' => ['Muzafarabad-01', 'Muzafrabad'],
            'zone letter' => ['Gujro Zone C', 'Gujro'],
            'bare zone letter' => ['Zone E', 'Gujro'],
            'numeric prefix' => ['05 Songal', 'Songal'],
            'bare number' => ['09', 'Islamia Colony-09'],
            'lowercase spelling' => ['Islamia colony-9', 'Islamia Colony-09'],
            'dropped letter' => ['Chisti Nagar-7', 'Chishti Nagar-7'],
            'reordered manghopir' => ['Manghopir-8', 'UC 8 Manghopir'],
            'shortened manghopir' => ['Mangopir - 8', 'UC 8 Manghopir'],
        ];
    }

    #[DataProvider('variantProvider')]
    public function test_it_maps_each_known_variant_to_its_consolidated_name(string $raw, string $expected): void
    {
        $this->assertSame($expected, DashboardController::getConsolidatedUcName($raw));
    }

    public function test_matching_is_case_insensitive(): void
    {
        $this->assertSame('Gujro', DashboardController::getConsolidatedUcName('gujro zone c'));
        $this->assertSame('Gujro', DashboardController::getConsolidatedUcName('GUJRO ZONE C'));
    }

    public function test_an_unknown_uc_passes_through_unchanged(): void
    {
        // Deliberate: a UC we have no mapping for must still be reportable
        // under its own name rather than vanishing or collapsing into another.
        $this->assertSame('Some New UC', DashboardController::getConsolidatedUcName('Some New UC'));
    }

    public function test_null_and_empty_input_yield_null(): void
    {
        $this->assertNull(DashboardController::getConsolidatedUcName(null));
        $this->assertNull(DashboardController::getConsolidatedUcName(''));
    }

    public function test_variants_lookup_returns_every_spelling_for_a_consolidated_uc(): void
    {
        $variants = DashboardController::getUcVariants('Muzafrabad');

        $this->assertContains('Muzafrabad', $variants);
        $this->assertContains('Muzafarabad', $variants);
        $this->assertContains('Muzafarabad-01', $variants);
    }

    public function test_variants_lookup_falls_back_to_the_name_itself(): void
    {
        $this->assertSame(['Some New UC'], DashboardController::getUcVariants('Some New UC'));
    }

    /**
     * Every variant must round-trip: consolidating it then expanding the result
     * has to yield a list that still contains the original spelling. Without
     * this, a record filed under that spelling drops out of its own UC report.
     */
    public function test_every_variant_round_trips_through_consolidation(): void
    {
        foreach (self::variantProvider() as $case) {
            [$raw, $consolidated] = $case;

            $this->assertContains(
                $raw,
                DashboardController::getUcVariants($consolidated),
                "Variant [{$raw}] is not reachable from consolidated name [{$consolidated}]."
            );
        }
    }
}
