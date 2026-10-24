<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The admin maps broke in Oct 2026 when the CARTO key stopped being accepted:
 * every tile 403'd and the maps rendered as empty boxes. These pin down the
 * arrangement that keeps them drawing — a keyless default, and a fallback the
 * page can switch to if the configured provider stops serving tiles.
 */
class BasemapConfigTest extends TestCase
{
    use RefreshDatabase;

    private const OSM_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

    /** Reload config/services.php with a given CARTO key in the environment. */
    private function basemapWithKey(?string $key): array
    {
        $previous = getenv('CARTO_BASEMAP_KEY');

        if ($key === null) {
            putenv('CARTO_BASEMAP_KEY');
            unset($_ENV['CARTO_BASEMAP_KEY'], $_SERVER['CARTO_BASEMAP_KEY']);
        } else {
            putenv("CARTO_BASEMAP_KEY={$key}");
            $_ENV['CARTO_BASEMAP_KEY'] = $key;
            $_SERVER['CARTO_BASEMAP_KEY'] = $key;
        }

        $config = require config_path('services.php');

        if ($previous === false) {
            putenv('CARTO_BASEMAP_KEY');
            unset($_ENV['CARTO_BASEMAP_KEY'], $_SERVER['CARTO_BASEMAP_KEY']);
        } else {
            putenv("CARTO_BASEMAP_KEY={$previous}");
            $_ENV['CARTO_BASEMAP_KEY'] = $previous;
            $_SERVER['CARTO_BASEMAP_KEY'] = $previous;
        }

        return $config['maps'];
    }

    public function test_without_a_key_the_basemap_is_the_keyless_provider(): void
    {
        $maps = $this->basemapWithKey(null);

        $this->assertSame(self::OSM_URL, $maps['basemap']['url']);
        $this->assertStringNotContainsString('key=', $maps['basemap']['url']);
    }

    public function test_an_empty_key_is_treated_as_no_key(): void
    {
        // An operator clearing the value must not produce '?key=' with nothing
        // after it, which CARTO rejects just like a wrong key.
        $maps = $this->basemapWithKey('');

        $this->assertSame(self::OSM_URL, $maps['basemap']['url']);
    }

    public function test_a_key_switches_the_basemap_to_carto(): void
    {
        $maps = $this->basemapWithKey('test-key-123');

        $this->assertStringContainsString('basemaps.cartocdn.com', $maps['basemap']['url']);
        $this->assertStringContainsString('?key=test-key-123', $maps['basemap']['url']);
    }

    /**
     * Subdomains must travel with the URL: CARTO serves a-d but OpenStreetMap
     * only a-c, and d.tile.openstreetmap.org does not resolve — sharing one
     * 'abcd' across both silently fails a quarter of the tiles.
     */
    public function test_each_provider_carries_its_own_subdomains(): void
    {
        $this->assertSame('abc', $this->basemapWithKey(null)['basemap']['subdomains']);
        $this->assertSame('abcd', $this->basemapWithKey('test-key-123')['basemap']['subdomains']);
    }

    public function test_the_fallback_is_always_the_keyless_provider(): void
    {
        foreach ([null, '', 'test-key-123'] as $key) {
            $maps = $this->basemapWithKey($key);

            $this->assertSame(self::OSM_URL, $maps['fallback']['url']);
            $this->assertSame('abc', $maps['fallback']['subdomains']);
            $this->assertStringNotContainsString('key=', $maps['fallback']['url']);
        }
    }

    public function test_every_basemap_source_is_fully_specified(): void
    {
        foreach ([null, 'test-key-123'] as $key) {
            $maps = $this->basemapWithKey($key);

            foreach (['basemap', 'fallback'] as $which) {
                foreach (['url', 'subdomains', 'attribution', 'max_zoom'] as $field) {
                    $this->assertArrayHasKey($field, $maps[$which]);
                    $this->assertNotEmpty($maps[$which][$field], "{$which}.{$field} is empty.");
                }
                // OpenStreetMap's licence requires its attribution be shown.
                $this->assertStringContainsString('OpenStreetMap', $maps[$which]['attribution']);
            }
        }
    }

    // ------------------------------------------------------------- Rendering

    public function test_the_map_pages_ship_the_basemap_helper_and_both_sources(): void
    {
        $user = User::factory()->create();

        foreach (['admin.outreach-sites.index', 'admin.fgds-community.index'] as $route) {
            $response = $this->actingAs($user)->get(route($route));

            $response->assertOk();
            // The helper is defined once in the layout and called by each map.
            $response->assertSee('clmAddBasemap', false);
            // Both the primary and the fallback have to reach the page, or the
            // switch has nothing to switch to.
            $response->assertSee('tile.openstreetmap.org', false);
        }
    }

    public function test_the_helper_is_defined_before_the_page_scripts_that_call_it(): void
    {
        $body = $this->actingAs(User::factory()->create())
            ->get(route('admin.outreach-sites.index'))
            ->assertOk()
            ->getContent();

        $definition = strpos($body, 'window.clmAddBasemap');
        $call = strpos($body, 'clmAddBasemap(map)');

        $this->assertNotFalse($definition, 'The basemap helper is never defined.');
        $this->assertNotFalse($call, 'The page never calls the basemap helper.');
        $this->assertLessThan($call, $definition, 'The helper is defined after the code that calls it.');
    }
}
