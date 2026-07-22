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

```bash
composer require vortechron/filament-hasnayeen
php artisan themes:install
php artisan migrate
```

If you installed the `vortechron/filament-themes` bundle, skip the Composer
command but still run the install and migration commands.

Register the plugin in the target panel provider:

```php
use Filament\Panel;
use Hasnayeen\Themes\ThemesPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(ThemesPlugin::make());
}
```

Publish package assets and clear cached configuration:

```bash
php artisan filament:assets
php artisan optimize:clear
```

The plugin registers its middleware, appearance page, user-menu item, and
styles automatically. Do not add `SetTheme` manually.

## Choose the storage mode

The published `config/themes.php` defaults to per-user mode:

```php
'mode' => 'user',
```

This stores `theme` and `theme_color` on each authenticated user and requires
the published migration.

For one shared choice stored in the application cache:

```php
'mode' => 'global',
```

Global mode does not read the user columns, but keeping the migration applied
makes switching modes safe.

## Restrict access

```php
->plugin(
    ThemesPlugin::make()
        ->canViewThemesPage(fn (): bool => auth()->user()?->is_admin === true)
)
```

## Register a custom theme

Create a class implementing `Hasnayeen\Themes\Contracts\Theme`, then register
it through the plugin:

```php
->plugin(
    ThemesPlugin::make()->registerTheme([
        'company' => \App\Filament\Themes\CompanyTheme::class,
    ])
)
```

The stylesheet returned by `getPath()` must be a local, production-built CSS
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
