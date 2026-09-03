<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ThemePackageSmokeTest extends TestCase
{
    public function test_login_page_renders_the_selected_theme_asset(): void
    {
        $assetPackage = (string) getenv('EXPECTED_THEME_ASSET');

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee($assetPackage, escape: false);
    }

    public function test_login_page_requests_no_third_party_font_cdn(): void
    {
        $html = $this->get('/admin/login')->assertOk()->getContent();

        foreach (['fonts.googleapis.com', 'fonts.gstatic.com', 'use.typekit.net'] as $host) {
            $this->assertStringNotContainsString(
                $host,
                (string) $html,
                "The panel must not load assets from {$host}. Bundle the font locally.",
            );
        }
    }
}
