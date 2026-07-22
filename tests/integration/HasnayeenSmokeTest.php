<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Vortechron\FilamentHasnayeen\ThemeManager;

class HasnayeenSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_appearance_page_renders_for_an_authenticated_user(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/appearance')
            ->assertOk()
            ->assertSee('Dracula')
            ->assertSee('Nord')
            ->assertSee('Sunset');
    }

    public function test_every_bundled_theme_resolves_with_colors_and_assets(): void
    {
        config()->set('filament-hasnayeen.mode', ThemeManager::GLOBAL_MODE);
        $manager = app(ThemeManager::class);

        foreach ($manager->all()->keys() as $name) {
            Cache::forever(ThemeManager::THEME_CACHE_KEY, $name);

            $theme = $manager->current();

            $this->assertSame($name, $theme->name());
            $this->assertFileIsReadable($theme->stylesheetPath());
            $this->assertNotEmpty($manager->currentColors());
        }
    }
}
