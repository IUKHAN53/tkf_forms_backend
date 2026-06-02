<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarrierCategory extends Model
{
    protected $table = 'barrier_categories';

    protected $fillable = [
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * The ONLY barrier categories that may exist. Barrier imports map every row
     * into one of these — they never create a new category. Keep in sync with
     * migration 2026_06_02_000002_update_barrier_categories_list.
     */
    public const CANONICAL = [
        'Misconceptions and Misinformation about Vaccines',
        'Fear of Side Effects and Vaccine Safety Concerns',
        'Forceful Vaccination and Consent Issues',
        'Poor Behavior and Communication of Health Workers',
        'Lack of Community Awareness and Health Education',
        'Lack of Trust in Health System and Government',
        'Inadequate Services at Health Facility and Infrastructure',
        'Lack of Essential Community Services',
        'Access Issues',
        'Recommendations and Demands from Community',
        'Religious and Cultural Beliefs',
    ];

    /**
     * Category chosen when an uploaded value matches nothing at all (blank cell
     * or zero keyword overlap). Must be one of self::CANONICAL.
     */
    public const FALLBACK = 'Lack of Community Awareness and Health Education';

    /**
     * Exact legacy / variant labels mapped to their closest canonical category.
     * Covers the old 8-category taxonomy and common spelling variants so any
     * historical file still lands inside the 11 instead of being mis-bucketed.
     * Keys are normalized via self::normalizeName().
     */
    private const ALIASES = [
        'cultural compatibility / traditional beliefs and practices' => 'Religious and Cultural Beliefs',
        'cultural compatibility traditional beliefs and practices'   => 'Religious and Cultural Beliefs',
        'communication / information'                                => 'Lack of Community Awareness and Health Education',
        'communication information'                                  => 'Lack of Community Awareness and Health Education',
        'service availability'                                       => 'Inadequate Services at Health Facility and Infrastructure',
        'system and procedures'                                      => 'Lack of Trust in Health System and Government',
        'client / provider relations'                                => 'Poor Behavior and Communication of Health Workers',
        'client provider relations'                                  => 'Poor Behavior and Communication of Health Workers',
        'provider technical competence'                              => 'Poor Behavior and Communication of Health Workers',
        'supplies and equipment / medicine'                          => 'Inadequate Services at Health Facility and Infrastructure',
        'supplies and equipment medicine'                            => 'Inadequate Services at Health Facility and Infrastructure',
        'place / environment'                                        => 'Access Issues',
        'place environment'                                          => 'Access Issues',
    ];

    /**
     * Keyword fingerprints used to pick the closest canonical category when an
     * uploaded value is neither an exact name nor a known alias.
     */
    private const KEYWORDS = [
        'Misconceptions and Misinformation about Vaccines'          => ['misconception', 'misinformation', 'myth', 'rumor', 'rumour', 'false', 'wrong information', 'cause harm', 'vaccine cause', 'vaccines cause', 'infertil'],
        'Fear of Side Effects and Vaccine Safety Concerns'          => ['fear', 'side effect', 'safety', 'adverse', 'reaction', 'aefi', 'unsafe'],
        'Forceful Vaccination and Consent Issues'                   => ['forceful', 'force', 'forced', 'consent', 'coerc', 'pressure', 'without permission'],
        'Poor Behavior and Communication of Health Workers'         => ['behavior', 'behaviour', 'communication', 'attitude', 'rude', 'unfriendly', 'misbehav', 'staff', 'provider', 'client', 'competence', 'harsh'],
        'Lack of Community Awareness and Health Education'           => ['awareness', 'education', 'knowledge', 'uneducated', 'illiterate', 'unaware', 'schedule', 'information', 'aware'],
        'Lack of Trust in Health System and Government'             => ['trust', 'distrust', 'mistrust', 'government', 'political', 'system', 'procedure'],
        'Inadequate Services at Health Facility and Infrastructure' => ['facility', 'infrastructure', 'equipment', 'supplies', 'supply', 'medicine', 'cold chain', 'building', 'service availability', 'inadequate service'],
        'Lack of Essential Community Services'                      => ['essential', 'water', 'sanitation', 'electricity', 'basic service', 'community service'],
        'Access Issues'                                             => ['access', 'accessib', 'reach', 'terrain', 'transport', 'mobility', 'distance', 'far', 'remote', 'road', 'place', 'environment'],
        'Recommendations and Demands from Community'                => ['recommendation', 'recommend', 'demand', 'suggestion', 'suggest', 'request', 'incentive'],
        'Religious and Cultural Beliefs'                            => ['religious', 'religion', 'cultural', 'culture', 'tradition', 'belief', 'faith', 'maulvi', 'fatwa', 'haram'],
    ];

    public function communityBarriers(): HasMany
    {
        return $this->hasMany(FgdsCommunityBarrier::class, 'barrier_category_id');
    }

    /**
     * Get categories ordered by sort_order
     */
    public static function ordered()
    {
        return static::orderBy('sort_order')->get();
    }

    /**
     * Normalize a category name for matching: lowercase, collapse whitespace,
     * strip trailing punctuation.
     */
    public static function normalizeName(string $name): string
    {
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $name)));
        return rtrim($normalized, '.,;:');
    }

    /**
     * Resolve an arbitrary uploaded category label to one of the canonical 11.
     *
     * Resolution order: exact (normalized) name → known alias → best keyword
     * overlap → self::FALLBACK. ALWAYS returns an existing canonical category and
     * NEVER creates a new one, so imports can't reintroduce stray/old categories.
     *
     * Pass $byNormalizedName (BarrierCategory keyBy normalized name) to avoid a
     * query per row during bulk import; it is built/refreshed automatically when
     * omitted.
     */
    public static function resolveForImport(string $rawName, $byNormalizedName = null): ?self
    {
        $byNormalizedName ??= static::all()->keyBy(fn ($c) => static::normalizeName($c->name));

        $resolve = function (string $canonicalName) use ($byNormalizedName) {
            return $byNormalizedName->get(static::normalizeName($canonicalName));
        };

        $normalized = static::normalizeName($rawName);

        // 1. Exact match against an existing canonical category.
        if ($normalized !== '' && $byNormalizedName->has($normalized)) {
            return $byNormalizedName->get($normalized);
        }

        // 2. Known legacy / variant alias.
        if (isset(self::ALIASES[$normalized])) {
            if ($cat = $resolve(self::ALIASES[$normalized])) {
                return $cat;
            }
        }

        // 3. Closest canonical by keyword overlap.
        if ($normalized !== '') {
            $bestName = null;
            $bestScore = 0;
            foreach (self::KEYWORDS as $canonicalName => $keywords) {
                $score = 0;
                foreach ($keywords as $kw) {
                    if (str_contains($normalized, $kw)) {
                        $score++;
                    }
                }
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestName = $canonicalName;
                }
            }
            if ($bestName && ($cat = $resolve($bestName))) {
                return $cat;
            }
        }

        // 4. Nothing matched — fall back to the designated default of the 11.
        return $resolve(self::FALLBACK) ?? $byNormalizedName->first();
    }
}
