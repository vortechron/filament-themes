<?php

declare(strict_types=1);

namespace Vortechron\FilamentHasnayeen;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LogicException;
use Vortechron\FilamentHasnayeen\Contracts\SupportsCustomColor;
use Vortechron\FilamentHasnayeen\Contracts\Theme;
use Vortechron\FilamentHasnayeen\Themes\DefaultTheme;
use Vortechron\FilamentHasnayeen\Themes\DraculaTheme;
use Vortechron\FilamentHasnayeen\Themes\NordTheme;
use Vortechron\FilamentHasnayeen\Themes\SunsetTheme;

class ThemeManager
{
    public const CONFIG_KEY = 'filament-hasnayeen';

    public const GLOBAL_MODE = 'global';

    public const USER_MODE = 'user';

    public const THEME_CACHE_KEY = 'filament-hasnayeen:theme';

    public const COLOR_CACHE_KEY = 'filament-hasnayeen:theme-color';

    /**
     * @var Collection<string, class-string<Theme>>
     */
    protected Collection $themes;

    public function __construct()
    {
        $this->themes = collect([
            'default' => DefaultTheme::class,
            'dracula' => DraculaTheme::class,
            'nord' => NordTheme::class,
            'sunset' => SunsetTheme::class,
        ]);
    }

    /**
     * @return Collection<string, class-string<Theme>>
     */
    public function all(): Collection
    {
        return $this->themes;
    }

    /**
     * @param  array<string, class-string<Theme>>  $themes
     */
    public function register(array $themes, bool $replace = false): static
    {
        if ($themes === []) {
            throw new InvalidArgumentException('At least one theme must be provided.');
        }

        foreach ($themes as $name => $themeClass) {
            if ((! preg_match('/^[a-z0-9-]+$/', $name)) || (! is_subclass_of($themeClass, Theme::class))) {
                throw new InvalidArgumentException("Invalid theme registration [{$name}].");
            }

            if (app($themeClass)->name() !== $name) {
                throw new InvalidArgumentException("Theme key [{$name}] must match the theme name.");
            }
        }

        $this->themes = $replace
            ? collect($themes)
            : $this->themes->merge($themes);

        return $this;
    }

    public function resolve(string $name): Theme
    {
        $themeClass = $this->themes->get($name) ?? $this->themes->first();

        if (! $themeClass) {
            throw new LogicException('No themes are registered.');
        }

        return app($themeClass);
    }

    public function current(): Theme
    {
        $default = (string) config(self::CONFIG_KEY.'.default.theme', 'default');

        if ($this->usesGlobalMode()) {
            return $this->resolve((string) (cache(self::THEME_CACHE_KEY) ?? $default));
        }

        $attribute = (string) config(self::CONFIG_KEY.'.user_attributes.theme', 'filament_theme');
        $selected = data_get(Filament::auth()->user(), $attribute);

        return $this->resolve(is_string($selected) ? $selected : $default);
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function currentColors(): array
    {
        $theme = $this->current();

        if (! $theme instanceof SupportsCustomColor) {
            return $theme->colors();
        }

        $default = config(self::CONFIG_KEY.'.default.color');
        $selected = $this->selectedColor();
        $color = is_string($selected) ? $selected : (is_string($default) ? $default : null);

        if ($color && Arr::has($theme->colors(), $color)) {
            return ['primary' => Arr::get($theme->colors(), $color)];
        }

        return $color ? ['primary' => $color] : $theme->defaultPrimaryColor();
    }

    public function selectedColor(): ?string
    {
        $selected = $this->usesGlobalMode()
            ? cache(self::COLOR_CACHE_KEY)
            : data_get(
                Filament::auth()->user(),
                (string) config(self::CONFIG_KEY.'.user_attributes.color', 'filament_theme_color'),
            );

        return is_string($selected) ? $selected : null;
    }

    public function saveColor(string $color): void
    {
        $this->savePreference('color', $color, self::COLOR_CACHE_KEY);
    }

    public function saveTheme(string $theme): void
    {
        if (! $this->themes->has($theme)) {
            throw new InvalidArgumentException("Theme [{$theme}] is not registered.");
        }

        $this->savePreference('theme', $theme, self::THEME_CACHE_KEY);
    }

    public function usesGlobalMode(): bool
    {
        return config(self::CONFIG_KEY.'.mode', self::USER_MODE) === self::GLOBAL_MODE;
    }

    protected function savePreference(string $preference, string $value, string $cacheKey): void
    {
        if ($this->usesGlobalMode()) {
            cache()->forever($cacheKey, $value);

            return;
        }

        $user = Filament::auth()->user();
        abort_unless($user instanceof Model, 403);

        $attribute = (string) config(
            self::CONFIG_KEY.".user_attributes.{$preference}",
            $preference === 'theme' ? 'filament_theme' : 'filament_theme_color',
        );

        $user->setAttribute($attribute, $value);
        $user->save();
    }
}
