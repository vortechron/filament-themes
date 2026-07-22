<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Vortechron\FilamentHasnayeen\Contracts\Theme;
use Vortechron\FilamentHasnayeen\HasnayeenPlugin;
use Vortechron\FilamentHasnayeen\ThemeManager;

class Appearance extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $title = 'Appearance';

    protected string $view = 'filament-hasnayeen::filament.pages.appearance';

    public function getTitle(): string|Htmlable
    {
        return __('filament-hasnayeen::themes.appearance');
    }

    public function mount(): void
    {
        abort_unless(HasnayeenPlugin::get()->isAppearancePageVisible(), 403);
    }

    /**
     * @return Collection<string, class-string<Theme>>
     */
    public function getThemes(): Collection
    {
        return $this->manager()->all();
    }

    public function getCurrentTheme(): Theme
    {
        return $this->manager()->current();
    }

    public function getColor(): ?string
    {
        return $this->manager()->selectedColor();
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

        $this->manager()->saveColor($color);

        Notification::make()
            ->title(__('filament-hasnayeen::themes.primary_color_set').' '.$color.'.')
            ->success()
            ->send();

        return $this->redirect(static::getUrl());
    }

    public function setTheme(string $theme): mixed
    {
        abort_unless($this->getThemes()->has($theme), 422, 'Invalid theme.');

        $this->manager()->saveTheme($theme);

        Notification::make()
            ->title(__('filament-hasnayeen::themes.theme_set_to').' '.$theme.'.')
            ->success()
            ->send();

        return $this->redirect(static::getUrl());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getFooter(): ?View
    {
        return view('filament-hasnayeen::filament.pages.appearance-footer');
    }

    protected function manager(): ThemeManager
    {
        return app(ThemeManager::class);
    }
}
