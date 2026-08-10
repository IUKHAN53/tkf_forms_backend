<?php

namespace Tests\Feature;

use App\Models\BarrierCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Barrier imports must land inside the canonical 11 categories no matter what
 * an uploaded spreadsheet says. If resolution ever creates a category, the
 * dashboards and drill-down cards silently grow rows nobody can reconcile.
 */
class BarrierCategoryResolutionTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_migrations_seed_exactly_the_canonical_categories(): void
    {
        $names = BarrierCategory::ordered()->pluck('name')->all();

        $this->assertEqualsCanonicalizing(BarrierCategory::CANONICAL, $names);
        $this->assertCount(11, $names);
    }

    public function test_the_declared_fallback_is_itself_canonical(): void
    {
        $this->assertContains(BarrierCategory::FALLBACK, BarrierCategory::CANONICAL);
    }

    public function test_an_exact_name_resolves_to_that_category(): void
    {
        $category = BarrierCategory::resolveForImport('Access Issues');

        $this->assertSame('Access Issues', $category->name);
    }

    public function test_resolution_ignores_case_whitespace_and_trailing_punctuation(): void
    {
        foreach (['  access   issues ', 'ACCESS ISSUES', 'Access Issues.'] as $messy) {
            $this->assertSame(
                'Access Issues',
                BarrierCategory::resolveForImport($messy)->name,
                "Failed to normalize [{$messy}]."
            );
        }
    }

    public function test_a_legacy_eight_category_label_maps_onto_its_canonical_successor(): void
    {
        // These are the labels from the old taxonomy that still show up in
        // historical files staff re-upload.
        $expectations = [
            'Cultural Compatibility / Traditional beliefs and practices' => 'Religious and Cultural Beliefs',
            'Communication / Information' => 'Lack of Community Awareness and Health Education',
            'Service Availability' => 'Inadequate Services at Health Facility and Infrastructure',
            'System and Procedures' => 'Lack of Trust in Health System and Government',
            'Client / Provider Relations' => 'Poor Behavior and Communication of Health Workers',
            'Place / Environment' => 'Access Issues',
        ];

        foreach ($expectations as $legacy => $expected) {
            $this->assertSame(
                $expected,
                BarrierCategory::resolveForImport($legacy)->name,
                "Legacy label [{$legacy}] resolved to the wrong category."
            );
        }
    }

    public function test_a_free_text_label_resolves_by_keyword_overlap(): void
    {
        $expectations = [
            'Parents are afraid of side effects' => 'Fear of Side Effects and Vaccine Safety Concerns',
            'Rumours that the vaccine causes infertility' => 'Misconceptions and Misinformation about Vaccines',
            'Team vaccinated the child without consent' => 'Forceful Vaccination and Consent Issues',
            'The road is impassable and the site is too far' => 'Access Issues',
            'Religious leaders declared it haram' => 'Religious and Cultural Beliefs',
        ];

        foreach ($expectations as $raw => $expected) {
            $this->assertSame(
                $expected,
                BarrierCategory::resolveForImport($raw)->name,
                "Free-text label [{$raw}] resolved to the wrong category."
            );
        }
    }

    public function test_an_unmatchable_label_lands_on_the_fallback(): void
    {
        $this->assertSame(
            BarrierCategory::FALLBACK,
            BarrierCategory::resolveForImport('zzzz qqqq no overlap at all')->name
        );
    }

    public function test_a_blank_label_lands_on_the_fallback(): void
    {
        $this->assertSame(BarrierCategory::FALLBACK, BarrierCategory::resolveForImport('')->name);
        $this->assertSame(BarrierCategory::FALLBACK, BarrierCategory::resolveForImport('   ')->name);
    }

    /**
     * The load-bearing invariant: whatever a spreadsheet contains, resolution
     * returns one of the existing 11 and the table does not grow.
     */
    public function test_resolution_never_creates_a_category(): void
    {
        $before = BarrierCategory::count();

        $labels = [
            '', '   ', 'Totally Unknown Category', 'Service Availability',
            'Parents are afraid', '12345', 'Access Issues', 'zzz',
        ];

        foreach ($labels as $label) {
            $resolved = BarrierCategory::resolveForImport($label);

            $this->assertNotNull($resolved, "Label [{$label}] resolved to null.");
            $this->assertContains(
                $resolved->name,
                BarrierCategory::CANONICAL,
                "Label [{$label}] resolved outside the canonical set."
            );
        }

        $this->assertSame($before, BarrierCategory::count(), 'Resolution created a new category.');
    }

    public function test_a_prebuilt_lookup_gives_the_same_answer_as_an_ad_hoc_one(): void
    {
        // Bulk imports pass a prebuilt map to avoid a query per row; it must not
        // change the outcome.
        $lookup = BarrierCategory::all()->keyBy(fn ($c) => BarrierCategory::normalizeName($c->name));

        foreach (['Service Availability', 'Parents are afraid', 'nonsense'] as $label) {
            $this->assertSame(
                BarrierCategory::resolveForImport($label)->id,
                BarrierCategory::resolveForImport($label, $lookup)->id,
                "Prebuilt lookup disagreed for [{$label}]."
            );
        }
    }
}
