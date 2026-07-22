# Filament Boron Theme

A neubrutalist admin theme for **Filament 5** with Lexend typography, warm
cream surfaces, sage accents, a black sidebar, and hard offset shadows.

## Requirements

- PHP `8.2+`
- Filament `5.x`
- A deployment step that runs `php artisan filament:assets`

## Install

Install the individual package:

```bash
composer require vortechron/filament-boron
```

If you installed the `vortechron/filament-themes` bundle, skip that command.

Register Boron in the target panel provider:

```php
use Filament\Panel;
use Vortechron\FilamentBoron\BoronPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(BoronPlugin::make());
}
```

Publish Filament's package assets:

```bash
php artisan filament:assets
php artisan optimize:clear
```

No npm or Vite change is required.

## Options

Boron sets its color palette, loads Lexend, and registers its stylesheet. Keep
part of the panel's existing design with:

```php
BoronPlugin::make()
    ->withoutColors()
    ->withoutFont();
```

Use `->withoutStyles()` only when you load a customized Boron stylesheet
yourself.

## Customize with a Filament theme build

Publish editable source files:

```bash
php artisan vendor:publish --tag=filament-boron-theme
```

This creates:

- `resources/css/filament/boron/theme.css`
- `resources/css/filament/boron/boron.css`

Add `theme.css` to the Laravel Vite input, register it with
`->viteTheme('resources/css/filament/boron/theme.css')`, and disable the
package stylesheet:

```php
->viteTheme('resources/css/filament/boron/theme.css')
->plugin(BoronPlugin::make()->withoutStyles())
```

Then run `npm run build`. Override the `--boron-*` variables at the bottom of
`theme.css`; no PHP changes are needed.

## Production check

Confirm the panel login, sidebar, tables, forms, dropdowns, modals, mobile
navigation, and both light and dark mode after each Filament major upgrade.

## License

MIT.

