<?php

declare(strict_types=1);

namespace Hasnayeen\Themes\Filament\Pages;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Hasnayeen\Themes\Contracts\Theme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Themes extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $title = 'Appearance';

    protected string $view = 'themes::filament.pages.themes';

    public function getTitle(): string|Htmlable
    {
        return __('themes::themes.appearance');
    }

    public function mount(): void
    {
        abort_unless(ThemesPlugin::canView(), 403);
    }

    /**
     * @return Collection<string, class-string<Theme>>
     */
    public function getThemes(): Collection
    {
        return app(\Hasnayeen\Themes\Themes::class)->getThemes();
    }

    public function getCurrentTheme(): Theme
    {
        return app(\Hasnayeen\Themes\Themes::class)->getCurrentTheme();
    }

    public function getColor(): ?string
    {
        if (config('themes.mode') === 'global') {
            return cache('theme_color');
        }

        return Filament::auth()->user()?->theme_color;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function getColors(): array
    {
        return Arr::except(Color::all(), ['gray', 'zinc', 'neutral', 'stone']);
    }

    public function setColor(string $color): mixed
    {
        abort_unless(
            array_key_exists($color, $this->getColors()) || preg_match('/^#[0-9a-fA-F]{6}$/', $color),
            422,
            'Invalid theme color.',
        );

        if (config('themes.mode') === 'global') {
            cache(['theme_color' => $color]);
        } else {
            $user = Filament::auth()->user();
            abort_unless($user, 403);
            $user->theme_color = $color;
            $user->save();
        }

        Notification::make()
            ->title(__('themes::themes.primary_color_set').' '.$color.'.')
            ->success()
            ->send();

        return $this->redirect(self::getUrl());
    }

    public function setTheme(string $theme): mixed
    {
        abort_unless($this->getThemes()->has($theme), 422, 'Invalid theme.');

        if (config('themes.mode') === 'global') {
            cache(['theme' => $theme]);
        } else {
            $user = Filament::auth()->user();
            abort_unless($user, 403);
            $user->theme = $theme;
            $user->save();
        }

        Notification::make()
            ->title(__('themes::themes.theme_set_to').' '.$theme.'.')
            ->success()
            ->send();

        return $this->redirect(self::getUrl());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getFooter(): ?View
    {
        return view('themes::filament.pages.themes-footer');
    }
}
