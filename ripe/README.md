# Ripe Theme for Filament 5

A compact Stripe-dashboard inspired theme for **Filament 5**: purple accents,
soft white cards, hairline borders, dense tables, and dark navy surfaces.

## Requirements

- PHP `8.2+`
- Filament `5.x`
- A deployment step that runs `php artisan filament:assets`

## Install

```bash
composer require vortechron/filament-ripe
```

If you installed the `vortechron/filament-themes` bundle, skip that command.

Register Ripe in the target panel provider:

```php
use Filament\Panel;
use Ripe\Theme\RipeThemePlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugin(RipeThemePlugin::make());
}
```

Publish Filament's package assets:

```bash
php artisan filament:assets
php artisan optimize:clear
```

No npm or Vite change is required in the consuming application.

## Customize

Ripe's design tokens are CSS variables. Override them in the application's
Filament theme with the more specific `.fi-body` selector:

```css
.fi-body {
    --ripe-primary: #00b4d8;
    --ripe-radius-lg: 12px;
    --ripe-canvas: #f4f6fb;
}
```

The complete token list is at the top of
`resources/css/_overrides.css` in this package.

## Package development

The consuming application does not run these commands. They are only for
maintainers changing Ripe's source CSS:

```bash
npm ci
npm run build
```

Commit the rebuilt `dist/theme.css` with every source CSS change.

## Production check

Confirm the panel login, sidebar, tables, forms, dropdowns, modals, mobile
navigation, and both light and dark mode after each Filament major upgrade.

## License

MIT.

