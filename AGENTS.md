# Filament Themes contributor guide

## Scope

This is a Composer monorepo containing four independently installable Filament
5 packages:

- `boron/` — `vortechron/filament-boron` (visual theme)
- `ripe/` — `vortechron/filament-ripe` (visual theme)
- `material/` — `vortechron/filament-material` (visual theme)
- `hasnayeen/` — `vortechron/filament-hasnayeen` (theme switcher)

The root package, `vortechron/filament-themes`, installs all four. A consuming
panel must register only one visual theme.

## Two kinds of package

Every directory is one of exactly two kinds. Pick the kind first, then follow
that kind's contract. Do not invent a third kind.

**Visual theme** — one fixed look applied to a panel. Boron, Ripe, Material.
**Theme switcher** — lets an end user pick between several looks at runtime.
Hasnayeen. There is currently one, and a second one needs a strong reason.

## Compatibility rules

- Target PHP `^8.2` and Filament `^5.0` until a major-version upgrade is
  explicitly tested.
- Use current Filament 5 hooks such as `.fi-sidebar-item.fi-active
  .fi-sidebar-item-btn`, `.fi-tabs-item.fi-active`, and `.fi-btn.fi-color-*`.
- Do not claim compatibility with an untested Filament major.
- Register package assets with Filament's asset manager and use the Composer
  package name as the asset package key.
- Keep runtime assets local. Do not depend on third-party CDNs or preview URLs.
  This includes fonts: no `@import url("https://fonts.googleapis.com/...")`.
  `scripts/verify.sh` fails the build if a remote `@import` appears in any
  theme's CSS.

## Naming rules

For a theme named `Boron`:

| Thing | Value |
| --- | --- |
| Directory | `boron/` |
| Composer package | `vortechron/filament-boron` |
| PHP namespace | `Vortechron\FilamentBoron` |
| Plugin class | `BoronPlugin` |
| Service provider | `BoronServiceProvider` |
| Token class | `BoronTheme` |
| Filament plugin ID | `vortechron-boron` |
| Filament asset package key | `vortechron/filament-boron` |
| Spatie package name | `filament-boron` |
| Publish tag | `filament-boron-theme` |
| Published CSS directory | `resources/css/filament/boron/` |

Substitute the theme name and apply the same pattern everywhere. No exceptions.

## Required package layout

A **visual theme** directory must contain exactly this shape:

```
{theme}/
  .gitignore                      same four lines as every other theme
  LICENSE.md
  README.md                       section order below
  composer.json
  package.json                    only if the theme has a build step
  resources/
    css/                          source CSS
    dist/theme.css                committed build output (build-step themes)
    fonts/{family}/               index.css + woff2 + LICENSE (if bundling a font)
    stubs/theme.css               the Vite publish stub
  src/
    {Theme}Plugin.php
    {Theme}ServiceProvider.php
    {Theme}Theme.php
```

A **theme switcher** replaces `src/{Theme}Theme.php` with a `src/Themes/`
directory and a `src/Contracts/Theme.php` interface, and may add `config/`,
`database/`, `resources/lang/` and `resources/views/`. Everything else in the
list still applies.

## Package rules

- Keep each subdirectory installable on its own; update both its manifest and
  README when its install contract changes.
- Add `declare(strict_types=1);` to source PHP classes and follow PSR-4 exactly.
- Use `spatie/laravel-package-tools` for package service providers.
- Add the new PSR-4 namespace and service provider to the root `composer.json`.
- Keep generated production CSS in `resources/dist/` committed, and rebuild it
  in the same change that edits the source CSS.
- Do not commit `vendor/` or `node_modules/`.
- Do not add a frontend build step to the consuming app unless the package
  cannot ship a safe prebuilt asset.
- Preserve original author attribution when porting a third-party theme.

## CSS rules

Default to the **build-step layout**, the one Ripe and Material use:

- Source modules in `resources/css/`, one concern per file: `_tokens.css`,
  `_foundations.css`, `_navigation.css`, `_components.css`, `_forms.css`,
  `_data.css`, `_feedback.css`, `_patterns.css`, `_dark.css`.
- `_overrides.css` imports them in that order and is the stable import target.
- `dist-entry.css` imports `_overrides.css` and is the esbuild entrypoint.
- `package.json` runs esbuild with the same three scripts every other theme has:
  `build`, `build:pretty`, `dev`.
- Commit the built `resources/dist/theme.css`.

Use the **single-file layout** (`resources/css/theme.css`, no `package.json`,
no `dist/`) only when the theme is small enough to stay readable in one file.
Boron is the one example. Do not mix the two layouts in one package.

Name every custom property `--{theme}-*` and define the light scheme on
`.fi-body`, the dark scheme on `.dark .fi-body`.

## Plugin API contract

Every **visual theme** plugin exposes the same surface:

```php
class {Theme}Plugin implements Plugin
{
    public const ASSET_PACKAGE = 'vortechron/filament-{theme}';
    public const ID = 'vortechron-{theme}';

    public static function make(): static;
    public function getId(): string;
    public function withoutColors(): static;   // always
    public function withoutFont(): static;     // only if the package bundles a font
    public function withoutStyles(): static;   // always
    public function register(Panel $panel): void;
    public function boot(Panel $panel): void;
}
```

- Every `without*()` method sets a `protected bool $apply*` property and
  returns `$this`.
- `register()` wraps each part in `if ($this->apply*)`.
- Never hardcode a colour inside `register()`. Read it from
  `{Theme}Theme::colors()`, which returns `array<string, string>` of hex values.
- Only add `withoutFont()` when `resources/fonts/` exists. A toggle that turns
  nothing off is worse than no toggle.

## Bundling a font

Never link a font from a CDN. Bundle it:

1. `npm install --save-dev @fontsource-variable/{family}` inside the theme.
2. Copy the `-wght-normal.woff2` subsets you need from `files/` into
   `resources/fonts/{family}/`.
3. Copy that package's `LICENSE` next to them.
4. Hand-write `resources/fonts/{family}/index.css` with one `@font-face` per
   subset, `font-family: '{Family}'`, `font-weight: 100 900`,
   `font-display: swap`, and the matching `unicode-range`.
5. `npm uninstall @fontsource-variable/{family}` — the copy is the artifact, the
   dependency is not.
6. Load it in the plugin with `Font::make()` plus `LocalFontProvider`, gated on
   `$this->applyFont`.
7. State the real font licence in the theme README. Check the bundled `LICENSE`
   file; do not assume.

## README structure

Every theme README uses these headings, in this order. Extra sections are
allowed between them; the required ones may not be reordered or dropped.

1. `# {Theme} — {one-line description} for Filament 5`
2. `## Requirements`
3. `## Install`
4. `## Register`
5. `## Options`
6. `## Customize`
7. `## Package development`
8. `## Production check`
9. `## License`

Use American spelling in prose (`color`, `customize`, `license`).

## Adding a theme

1. Decide the kind: visual theme or theme switcher.
2. Create the directory and build the required layout above.
3. Apply every row of the naming table.
4. Write `{Theme}Theme::colors()` first; the plugin reads from it.
5. Implement the plugin API contract, including the `without*()` toggles.
6. Add `packageBooted()` to the service provider publishing the stub and the
   stylesheet under the `filament-{theme}-theme` tag.
7. Bundle any font locally following the steps above.
8. Add the PSR-4 namespace and the service provider to the root
   `composer.json`.
9. Add the theme to the root `README.md`: the "pick one theme" section, the
   "Pick a theme" table, and the "Consistent package API" table.
10. Write the theme README with the required section order.
11. Add the theme's asset and token checks to `scripts/verify.sh`, and add it to
    the `THEMES` and `VISUAL_THEMES` lists at the top of that script.
12. Add one line to the `VISUAL_THEMES` list at the top of
    `scripts/verify-integration.sh`. Nothing else in that file needs editing.
13. Run `./scripts/verify.sh`, then `./scripts/verify-integration.sh`.
14. Test a real Filament 5 panel in light and dark mode before release.

## Verification

Run:

```bash
./scripts/verify.sh
```

It validates every Composer manifest, lints all PHP, enforces the naming rules,
the required package layout, the plugin API contract and the README section
order, checks required assets, rejects remote CSS imports, and rebuilds the
committed Ripe and Material stylesheets.

For UI changes, also test login, dashboard, navigation, tables, forms,
dropdowns, modals, mobile navigation, and dark mode in a real Filament 5 app.
Before a release, run `./scripts/verify-integration.sh` to test all clean
consumer installs.
