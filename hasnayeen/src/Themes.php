<?php

declare(strict_types=1);

namespace Hasnayeen\Themes;

use Filament\Facades\Filament;
use Hasnayeen\Themes\Contracts\HasChangeableColor;
use Hasnayeen\Themes\Contracts\Theme;
use Hasnayeen\Themes\Themes\DefaultTheme;
use Hasnayeen\Themes\Themes\Dracula;
use Hasnayeen\Themes\Themes\Nord;
use Hasnayeen\Themes\Themes\Sunset;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Themes
{
    /**
     * @var Collection<string, class-string<Theme>>
     */
    protected Collection $collection;

    public function __construct()
    {
        $this->collection = collect([
            DefaultTheme::getName() => DefaultTheme::class,
            Dracula::getName() => Dracula::class,
            Nord::getName() => Nord::class,
            Sunset::getName() => Sunset::class,
        ]);
    }

    /**
     * @return Collection<string, class-string<Theme>>
     */
    public function getThemes(): Collection
    {
        return $this->collection;
    }

    /**
     * @param  array<string, class-string<Theme>>  $themes
     */
    public function register(array $themes, bool $override = false): self
    {
        if (empty($themes)) {
            throw new InvalidArgumentException('No themes provided.');
        }

        if ($override) {
            $this->collection = collect($themes);

            return $this;
        }

        $this->collection = $this->collection->merge($themes);

        return $this;
    }

    public function make(string $theme): Theme
    {
        $name = $this->collection->first(fn (string $item): bool => $item::getName() === $theme);

        if ($name) {
            return new $name;
        }

        return app($this->collection->first());
    }

    public function getCurrentTheme(): Theme
    {
        if (config('themes.mode') === 'global') {
            return $this->make(cache('theme') ?? config('themes.default.theme', 'default'));
        }

        $user = Filament::auth()->user();

        return $this->make($user?->theme ?? config('themes.default.theme', 'default'));
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function getCurrentThemeColor(): array
    {
        $currentTheme = $this->getCurrentTheme();

        if (! $currentTheme instanceof HasChangeableColor) {
            return $currentTheme->getThemeColor();
        }

        if (config('themes.mode') === 'global') {
            $color = cache('theme_color') ?? config('themes.default.theme_color');
        } else {
            $color = Filament::auth()->user()?->theme_color ?? config('themes.default.theme_color');
        }

        return Arr::has($currentTheme->getThemeColor(), $color)
            ? ['primary' => Arr::get($currentTheme->getThemeColor(), $color)]
            : ($color ? ['primary' => $color] : $currentTheme->getPrimaryColor());
    }
}
