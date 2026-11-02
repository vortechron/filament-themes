# Boron — neubrutalist theme for Filament 5

A neubrutalist admin theme for **Filament 5** with Lexend typography, warm
cream surfaces, sage accents, a black sidebar, and hard offset shadows.

## Requirements

- PHP `8.2+`
- Filament `5.x`
- A deployment step that runs `php artisan filament:assets`

## Install

When Boron is published as an independent package, install it with:

```bash
composer require vortechron/filament-boron
```

For this monorepo, install the `vortechron/filament-themes` bundle as shown in
the [root installation guide](../README.md#start-here-pick-one-theme), or use
the root guide's local path-repository instructions to require only Boron.

## Register

Append **one** plugin call to the existing panel chain in
`app/Providers/Filament/AdminPanelProvider.php`:

```php
use Filament\Panel;
use Vortechron\FilamentBoron\BoronPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... your existing configuration
        ->plugin(BoronPlugin::make());
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

Boron sets its color palette, loads its bundled local Lexend font, and
registers its stylesheet. Each part can be turned off on its own:

```php
BoronPlugin::make()
    ->withoutColors() // keep the panel's existing color palette
    ->withoutFont()   // keep the panel's existing font
    ->withoutStyles() // load the palette and font only, no stylesheet
```

Use `->withoutStyles()` only when you load a customized Boron stylesheet
yourself.

### Branded sign-in pages

The sign-in, sign-up and password pages show a centered card by default. Add a
branded panel beside the form (a banner above it on phones):

```php
BoronPlugin::make()
    ->authBranding(
        heading: 'Acme Store',                    // defaults to the panel brand name
        description: 'Manage your store website.', // optional
        image: asset('images/login.jpg'),          // optional, fills the panel
    )
```

The panel replaces the brand name at the top of the form, so the name shows
once. Text is escaped. The image sits behind a dark overlay so the text stays
readable.

## Customize

Boron's design tokens are CSS variables. Override the `--boron-*` variables in
the application's Filament theme with the more specific `.fi-body` selector.
The complete token list is in `resources/css/theme.css`, and the PHP palette
handed to Filament is in `src/BoronTheme.php`.

To extend the theme with your own Tailwind classes, publish the editable source
files:

```bash
php artisan vendor:publish --tag=filament-boron-theme
```

This writes:

- `resources/css/filament/boron/theme.css`
- `resources/css/filament/boron/boron.css`

Add `theme.css` to the Laravel Vite input, register the entrypoint on the panel,
and disable the packaged stylesheet:

```php
->viteTheme('resources/css/filament/boron/theme.css')
->plugin(BoronPlugin::make()->withoutStyles())
```

Then run `npm run build`. No PHP changes are needed.

## Package development

Boron ships hand-written CSS and has no build step. Edit
`resources/css/theme.css` directly; there is no `package.json` and nothing to
compile. Run `./scripts/verify.sh` from the repository root after any change.

## Production check

Confirm the panel login, sidebar, tables, forms, dropdowns, modals, mobile
navigation, and both light and dark mode after each Filament major upgrade.

## License

MIT.

Lexend is bundled under the SIL Open Font License; its license text is at
`resources/fonts/lexend/LICENSE`.
