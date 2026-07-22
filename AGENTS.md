# Filament Themes contributor guide

## Scope

This is a Composer monorepo containing three independently installable Filament
5 packages:

- `boron/` — `vortechron/filament-boron`
- `ripe/` — `vortechron/filament-ripe`
- `hasnayeen/` — `vortechron/filament-hasnayeen`

The root package, `vortechron/filament-themes`, installs all three. A consuming
panel must register only one visual theme.

## Compatibility rules

- Target PHP `^8.2` and Filament `^5.0` until a major-version upgrade is
  explicitly tested.
- Use current Filament 5 hooks such as `.fi-sidebar-item.fi-active
  .fi-sidebar-item-btn`, `.fi-tabs-item.fi-active`, and `.fi-btn.fi-color-*`.
- Do not claim compatibility with an untested Filament major.
- Register package assets with Filament's asset manager and use the Composer
  package name as the asset package key.
- Keep runtime assets local. Do not depend on third-party CDNs or preview URLs.

## Package rules

- Keep each subdirectory installable on its own; update both its manifest and
  README when its install contract changes.
- Keep generated production CSS in `resources/dist/` or `dist/` committed.
- Do not commit `vendor/` or `node_modules/`.
- Do not add a frontend build step to the consuming app unless the package
  cannot ship a safe prebuilt asset.
- Preserve original author attribution when porting a third-party theme.

## Adding a theme

1. Create an independent Composer package in a new directory.
2. Add its PSR-4 namespace and service provider to the root `composer.json`.
3. Add it to the root theme table and document exact panel registration.
4. Add required asset checks to `scripts/verify.sh`.
5. Test a real Filament 5 panel in light and dark mode before release.

## Verification

Run:

```bash
./scripts/verify.sh
```

For UI changes, also test login, dashboard, navigation, tables, forms,
dropdowns, modals, mobile navigation, and dark mode in a real Filament 5 app.

