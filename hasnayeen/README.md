# Hasnayeen Themes for Filament 5

A Filament 5 appearance switcher with four bundled themes: Default, Dracula,
Nord, and Sunset. It supports per-user preferences or one global preference.

This package is a maintained Filament 5 port of Hasnayeen's original Themes
package and preserves the original author attribution.

## Requirements

- PHP `8.2+`
- Filament `5.x`
- A `users` table for per-user mode

## Install

When Hasnayeen is published as an independent package, install it with:

```bash
composer require vortechron/filament-hasnayeen
```

For this monorepo, install the `vortechron/filament-themes` bundle as shown in
the [root installation guide](../README.md#start-here-pick-one-theme), or use
the root guide's local path-repository instructions to require only Hasnayeen.

Then run its setup commands:

```bash
php artisan filament-hasnayeen:install --no-interaction
php artisan migrate
```

Register the plugin in the target panel provider:

```php
use Filament\Panel;
use Vortechron\FilamentHasnayeen\HasnayeenPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(HasnayeenPlugin::make());
}
```

Publish package assets and clear cached configuration:

```bash
php artisan filament:assets
php artisan optimize:clear
```

The plugin registers its middleware, appearance page, user-menu item, and
styles automatically. Do not add `ApplyTheme` manually.

## Choose the storage mode

The published `config/filament-hasnayeen.php` defaults to per-user mode:

```php
'mode' => 'user',
```

This stores `filament_theme` and `filament_theme_color` on each authenticated
user and requires the published migration. Change `user_attributes` in the
config if the application uses different column names.

For one shared choice stored in the application cache:

```php
'mode' => 'global',
```

Global mode does not read the user columns, but keeping the migration applied
makes switching modes safe.

## Restrict access

```php
->plugin(
    HasnayeenPlugin::make()
        ->canViewAppearancePage(fn (): bool => auth()->user()?->is_admin === true)
)
```

## Register a custom theme

Create a class implementing
`Vortechron\FilamentHasnayeen\Contracts\Theme`, then register it through the
plugin:

```php
->plugin(
    HasnayeenPlugin::make()->registerThemes([
        'company' => \App\Filament\Themes\CompanyTheme::class,
    ])
)
```

The array key must match the value returned by `name()`. The stylesheet
returned by `stylesheetPath()` must be a readable, local, production-built CSS
file. After adding or changing theme assets, run `php artisan filament:assets`.

## Production notes

- The package does not load preview images or styles from third-party hosts.
- Selected theme values are validated before they are saved.
- Theme color variables and panel dark-mode state are resolved per request for
  Laravel Octane safety.
- If the configured cache is cleared in global mode, the configured default
  theme is used until someone selects a theme again.

## License

MIT. Original concept and package by Hasnayeen; Filament 5 port maintained by
Vortechron.
