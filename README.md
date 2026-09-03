# Vortechron Filament Themes

Four production-ready theme packages for **Filament 5**.

## Start here: pick one theme

Install this private repository once. It contains all four packages, but you
activate **exactly one** in each Filament panel.

This repository is private, so Composer needs a GitHub token before it can read
it. Create a token with the **`repo`** scope, then run this once per machine:

```bash
composer config --global github-oauth.github.com YOUR_GITHUB_TOKEN
```

Then add the repository and install the release:

```bash
composer config repositories.vortechron-filament-themes vcs https://github.com/vortechron/filament-themes.git
composer require vortechron/filament-themes:^1.0
```

Use the **HTTPS** URL, not `git@github.com:...`. With the SSH URL Composer
clones over SSH instead of using the GitHub API, which fails in non-interactive
shells and CI with `Host key verification failed`.

Then add the relevant import and append **one** plugin call to the existing
panel chain in `app/Providers/Filament/AdminPanelProvider.php`:

### Boron — bold neubrutalist admin UI

```php
use Vortechron\FilamentBoron\BoronPlugin;

// Append before the final semicolon in panel():
->plugin(BoronPlugin::make())
```

### Ripe — Stripe Apps SDK v9 visual system

```php
use Vortechron\FilamentRipe\RipePlugin;

// Append before the final semicolon in panel():
->plugin(RipePlugin::make())
```

### Material — Material Design 3 admin UI

```php
use Vortechron\FilamentMaterial\MaterialPlugin;

// Append before the final semicolon in panel():
->plugin(MaterialPlugin::make())
```

### Hasnayeen — users can choose Default, Dracula, Nord, or Sunset

```php
use Vortechron\FilamentHasnayeen\HasnayeenPlugin;

// Append before the final semicolon in panel():
->plugin(HasnayeenPlugin::make())
```

Run the setup command for the selected theme:

```bash
# Boron, Ripe or Material
php artisan filament:assets

# Hasnayeen
php artisan filament-hasnayeen:install --no-interaction
php artisan migrate
php artisan filament:assets
```

Do not register multiple theme plugins on the same panel. Hasnayeen already
contains four selectable visual themes and should be used by itself.

## Pick a theme

| Theme | Best for | Build step | Extra setup |
| --- | --- | --- | --- |
| [Boron](boron/README.md) | Neubrutalist cream, sage, and black admin UI | None | Register one plugin |
| [Ripe](ripe/README.md) | Stripe Apps SDK v9-style components and dashboards | None | Register one plugin |
| [Material](material/README.md) | Google Material Design 3 look: tonal palette, Roboto, state layers | None | Register one plugin |
| [Hasnayeen](hasnayeen/README.md) | Let users switch between Default, Dracula, Nord, and Sunset | None | Run its install command and migration |

## Consistent package API

Every theme follows the same naming convention:

| Theme | Composer package | PHP namespace | Panel plugin |
| --- | --- | --- | --- |
| Boron | `vortechron/filament-boron` | `Vortechron\FilamentBoron` | `BoronPlugin` |
| Ripe | `vortechron/filament-ripe` | `Vortechron\FilamentRipe` | `RipePlugin` |
| Material | `vortechron/filament-material` | `Vortechron\FilamentMaterial` | `MaterialPlugin` |
| Hasnayeen | `vortechron/filament-hasnayeen` | `Vortechron\FilamentHasnayeen` | `HasnayeenPlugin` |

Laravel discovers each package service provider automatically. Applications
only import the selected plugin class and add it to the target panel.

## Use a tagged release in production

`v1.0.0` is tagged. Add this repository to the Laravel application's
`composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vortechron/filament-themes.git"
        }
    ]
}
```

Then install it:

```bash
composer require vortechron/filament-themes:^1.0
```

To track the branch instead of a release, use
`composer require vortechron/filament-themes:dev-main`.

Your deployment environment also needs read access to this private repository.
Do not commit a token. Write `auth.json` next to `composer.json` at deploy time
and keep it out of git:

```json
{
    "github-oauth": {
        "github.com": "YOUR_GITHUB_TOKEN"
    }
}
```

Add `auth.json` to the application's `.gitignore`.

Use the panel registration and setup commands in [Start here: pick one theme](#start-here-pick-one-theme).

## Install only one package from a local checkout

Each theme directory is also an independent Composer package. Add a path
repository to the consuming application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../filament-themes/boron",
            "options": {
                "symlink": false
            }
        }
    ]
}
```

Replace `boron` with `ripe`, `material`, or `hasnayeen`, then require the
matching package:

```bash
composer require vortechron/filament-boron:@dev
# composer require vortechron/filament-ripe:@dev
# composer require vortechron/filament-material:@dev
# composer require vortechron/filament-hasnayeen:@dev
```

For public, independent package releases, split each directory into its own
tagged repository and submit that package to Packagist. Packagist does not
discover multiple package manifests inside one repository automatically.

## Production deploy checklist

1. Install dependencies with `composer install --no-dev --optimize-autoloader`.
2. Run `php artisan filament:assets` after every package update.
3. For Hasnayeen, run `php artisan filament-hasnayeen:install
   --no-interaction` and `php artisan migrate` once.
4. Clear only the application caches your deployment normally clears.
5. Verify the login page, dashboard, forms, tables, modals, and dark mode.

## Repository verification

Run before tagging a release:

```bash
./scripts/verify.sh
```

The script validates every Composer manifest, lints all PHP, enforces the
naming rules, package layout, plugin API and README section order defined in
[`AGENTS.md`](AGENTS.md), checks required assets, rejects remote CSS imports,
and confirms the committed Ripe and Material stylesheets are up to date.

Before a release, also run the clean consumer integration suite:

```bash
./scripts/verify-integration.sh
```

It installs each package independently in a temporary Laravel 12 / Filament 5
application and verifies provider discovery, panel boot, published assets, the
Vite publish stub, the absence of any third-party font CDN request, Hasnayeen's
migration, and its authenticated Appearance page.

## Support policy

- PHP: `8.2` and newer compatible releases
- Filament: `5.x`
- Laravel: versions supported by the installed Filament 5 release
- Browser support: the same modern browsers supported by Filament 5

Filament CSS hooks can change between major versions. A new Filament major must
be tested and released as a deliberate compatibility update.

## License

MIT. Hasnayeen includes attribution to its original author in its package.
Material bundles the Roboto font under the SIL Open Font License 1.1 and follows the
publicly published Material Design 3 specification; it is an independent
implementation and is not affiliated with or endorsed by Google.
