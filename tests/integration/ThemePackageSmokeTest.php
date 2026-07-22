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
}
