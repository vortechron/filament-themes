# Vortechron Filament Themes

Three production-ready theme packages for **Filament 5**. Install the bundle,
then register only the theme you want on each panel.

## Pick a theme

| Theme | Best for | Build step | Extra setup |
| --- | --- | --- | --- |
| [Boron](boron/README.md) | Neubrutalist cream, sage, and black admin UI | None | Register one plugin |
| [Ripe](ripe/README.md) | Compact Stripe-inspired dashboards | None | Register one plugin |
| [Hasnayeen](hasnayeen/README.md) | Let users switch between Default, Dracula, Nord, and Sunset | None | Publish config and migration |

Use **one visual theme per panel**. Do not register Boron or Ripe together on
the same panel. Hasnayeen is a switcher and should also be used by itself.

## Install the bundle from this private repository

Your deployment environment needs GitHub access to this private repository.
Add it to the Laravel application's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:vortechron/filament-themes.git"
        }
    ]
}
```

Install a tagged release:

```bash
composer require vortechron/filament-themes:^1.0
```

During development, before the first stable tag exists, use the repository's
default branch explicitly:

```bash
composer require vortechron/filament-themes:dev-main
```

Then follow the selected theme's README to register its plugin.

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

Replace `boron` with `ripe` or `hasnayeen`, then require the matching package:

```bash
composer require vortechron/filament-boron:@dev
# composer require vortechron/filament-ripe:@dev
# composer require vortechron/filament-hasnayeen:@dev
```

For public, independent package releases, split each directory into its own
tagged repository and submit that package to Packagist. Packagist does not
discover multiple package manifests inside one repository automatically.

## Production deploy checklist

1. Install dependencies with `composer install --no-dev --optimize-autoloader`.
2. Run `php artisan filament:assets` after every package update.
3. Run the selected theme's setup command, if its README has one.
4. Clear only the application caches your deployment normally clears.
5. Verify the login page, dashboard, forms, tables, modals, and dark mode.

## Repository verification

Run before tagging a release:

```bash
./scripts/verify.sh
```

The script validates every Composer manifest, lints all PHP, checks required
assets, and rebuilds Ripe's committed stylesheet.

## Support policy

- PHP: `8.2` and newer compatible releases
- Filament: `5.x`
- Laravel: versions supported by the installed Filament 5 release
- Browser support: the same modern browsers supported by Filament 5

Filament CSS hooks can change between major versions. A new Filament major must
be tested and released as a deliberate compatibility update.

## License

MIT. Hasnayeen includes attribution to its original author in its package.

