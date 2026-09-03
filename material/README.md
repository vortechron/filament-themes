# Material — Material Design 3 theme for Filament 5

An independent implementation of Google's publicly published
[Material Design 3](https://m3.material.io) specification for FilamentPHP 5
panels. It ships the baseline tonal color scheme, the Roboto type scale,
Material's shape and elevation scales, state layers, and the standard motion
easing curves.

Not affiliated with or endorsed by Google.

## Requirements

- PHP `8.2+`
- Filament `5.x`
- A deployment step that runs `php artisan filament:assets`

## What you get

- **Baseline tonal palette** — primary, secondary, tertiary, error and the full
  surface-container ramp, in matched light and dark schemes.
- **Roboto** — the variable font, bundled locally. No CDN request.
- **State layers** — hover, focus and pressed paint a translucent tint on top of
  the component instead of swapping its background color.
- **Material shape scale** — 4px fields, 12px cards, 28px dialogs, fully rounded
  buttons and navigation pills.
- **Material elevation** — the five spec shadow levels, deepened in dark mode.
- **Navigation drawer pill** — the active destination is a fully rounded
  `secondary-container` pill, the most recognizable part of the system.
- **Filled text fields** — a `surface-container-highest` box with rounded top
  corners and an active indicator that thickens to 2px on focus.
- **Snackbar notifications** — inverse-surface, elevation 3.

No build step is required in the consuming app. The stylesheet is prebuilt and
committed at `resources/dist/theme.css`.

## Install

When Material is published as an independent package, install it with:

```bash
composer require vortechron/filament-material
```

For this monorepo, install the `vortechron/filament-themes` bundle as shown in
the [root installation guide](../README.md#start-here-pick-one-theme), or use
the root guide's local path-repository instructions to require only Material.

## Register

Append **one** plugin call to the existing panel chain in
`app/Providers/Filament/AdminPanelProvider.php`:

```php
use Vortechron\FilamentMaterial\MaterialPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... your existing configuration
        ->plugin(MaterialPlugin::make());
}
```

Publish Filament's package assets:

```bash
php artisan filament:assets
php artisan optimize:clear
```

No npm or Vite change is required in the consuming application. Do not register
a second theme plugin on the same panel.

## Options

Each part of the theme can be turned off on its own:

```php
MaterialPlugin::make()
    ->withoutColors() // keep the panel's existing color palette
    ->withoutFont()   // keep the panel's existing font
    ->withoutStyles() // load the palette and font only, no stylesheet
```

## Customize

Material's design tokens are CSS variables. The stylesheet reads every color
from `--md-*` custom properties, so a single CSS block re-skins the whole panel.
Set the light scheme on `.fi-body` and the dark scheme on `.dark .fi-body`:

```css
.fi-body {
    --md-primary: #00639b;
    --md-on-primary: #ffffff;
    --md-primary-container: #cee5ff;
    --md-on-primary-container: #001d33;
    --md-secondary-container: #d7e3f8;
    --md-on-secondary-container: #101c2b;
}
```

Generate a matching set for any seed color with the
[Material Theme Builder](https://m3.material.io/theme-builder).

Keep `MaterialPlugin::make()->withoutColors()` off if you also want Filament's
own components (badges, buttons rendered by other plugins) to follow your seed;
pass your palette to the panel's `->colors()` call in that case.

The PHP palette handed to Filament is in `src/MaterialTheme.php`.

To extend the theme with your own Tailwind classes, publish the editable source
files:

```bash
php artisan vendor:publish --tag=filament-material-theme
```

This writes `resources/css/filament/material/theme.css` and
`resources/css/filament/material/material.css`. Register the entrypoint on the
panel with `->viteTheme('resources/css/filament/material/theme.css')`, disable
the packaged stylesheet with `MaterialPlugin::make()->withoutStyles()`, and
build it with Vite.

## Package development

Only needed when editing this package's own CSS:

```bash
npm ci
npm run build        # minified, writes resources/dist/theme.css
npm run build:pretty # readable output
npm run dev          # rebuild on change
```

Source modules live in `resources/css/`:

| File | Contents |
| --- | --- |
| `_tokens.css` | Color, type, shape, elevation, state and motion tokens |
| `_foundations.css` | Type scale, page surfaces, focus ring, state-layer helper |
| `_navigation.css` | Top app bar, navigation drawer, breadcrumbs, tabs |
| `_components.css` | Buttons, cards, dialogs, menus, chips, avatars |
| `_forms.css` | Filled text fields, checkboxes, radios, switches |
| `_data.css` | Tables, pagination, infolists, empty states |
| `_feedback.css` | Snackbars, callouts, wizard stepper, global search |
| `_patterns.css` | Login layout, stats widgets, charts |
| `_dark.css` | The dark color scheme |

Commit the rebuilt `resources/dist/theme.css` with every source CSS change.

## Production check

Confirm the panel login, sidebar, tables, forms, dropdowns, modals, mobile
navigation, and both light and dark mode after each Filament major upgrade.

## License

MIT.

Roboto is bundled under the SIL Open Font License 1.1; its license text is at
`resources/fonts/roboto/LICENSE`. Material makes no request to a third-party
CDN at runtime. Material Design 3 is a publicly published
specification by Google. This package is an independent implementation of that
specification and is not affiliated with or endorsed by Google.
