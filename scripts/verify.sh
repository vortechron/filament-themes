#!/usr/bin/env bash

set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

verify_package_contract() {
    package_dir="$1"
    namespace="$2"
    theme="$3"

    test -f "$package_dir/src/${theme}Plugin.php"
    test -f "$package_dir/src/${theme}ServiceProvider.php"
    grep -Fq "namespace ${namespace};" "$package_dir/src/${theme}Plugin.php"
    grep -Fq "namespace ${namespace};" "$package_dir/src/${theme}ServiceProvider.php"
    grep -Fq "public const ID = 'vortechron-$(printf '%s' "$theme" | tr '[:upper:]' '[:lower:]')';" \
        "$package_dir/src/${theme}Plugin.php"
}

for package_dir in "$repo_dir" "$repo_dir/boron" "$repo_dir/hasnayeen" "$repo_dir/ripe"; do
    composer validate --working-dir="$package_dir" --strict --no-check-publish
done

find "$repo_dir/boron" "$repo_dir/hasnayeen" "$repo_dir/ripe" \
    -path '*/vendor' -prune -o \
    -path '*/node_modules' -prune -o \
    -type f -name '*.php' -print0 \
    | xargs -0 -n1 php -l

while IFS= read -r -d '' php_file; do
    grep -Fq 'declare(strict_types=1);' "$php_file"
done < <(find "$repo_dir/boron/src" "$repo_dir/hasnayeen/src" "$repo_dir/ripe/src" \
    -type f -name '*.php' -print0)

verify_package_contract "$repo_dir/boron" 'Vortechron\FilamentBoron' 'Boron'
verify_package_contract "$repo_dir/hasnayeen" 'Vortechron\FilamentHasnayeen' 'Hasnayeen'
verify_package_contract "$repo_dir/ripe" 'Vortechron\FilamentRipe' 'Ripe'

if rg -n '^(namespace|use) (Ripe\\Theme|Hasnayeen\\Themes)' \
    "$repo_dir/boron/src" "$repo_dir/hasnayeen/src" "$repo_dir/ripe/src"; then
    echo 'Legacy package namespace found.' >&2
    exit 1
fi

test -s "$repo_dir/boron/resources/css/theme.css"
test -s "$repo_dir/boron/resources/fonts/lexend/index.css"
test -s "$repo_dir/boron/resources/fonts/lexend/lexend-latin-wght-normal.woff2"
test -s "$repo_dir/hasnayeen/resources/dist/appearance.css"
test -s "$repo_dir/hasnayeen/resources/dist/default.css"
test -s "$repo_dir/hasnayeen/resources/dist/dracula.css"
test -s "$repo_dir/hasnayeen/resources/dist/nord.css"
test -s "$repo_dir/hasnayeen/resources/dist/sunset.css"

for ripe_source in \
    _tokens.css \
    _foundations.css \
    _navigation.css \
    _components.css \
    _forms.css \
    _data.css \
    _feedback.css \
    _dark.css; do
    test -s "$repo_dir/ripe/resources/css/$ripe_source"
done

test -s "$repo_dir/ripe/STRIPE_APPS_V9_COVERAGE.md"
grep -Fq -- '--ripe-primary: #533afd;' "$repo_dir/ripe/resources/css/_tokens.css"
grep -Fq "Color::hex('#533afd')" "$repo_dir/ripe/src/RipePlugin.php"
grep -Fq '.fi-btn.fi-color-primary' "$repo_dir/ripe/resources/css/_components.css"
grep -Fq '.fi-tabs-item.fi-active' "$repo_dir/ripe/resources/css/_components.css"
grep -Fq '.fi-input-wrp.fi-invalid' "$repo_dir/ripe/resources/css/_forms.css"
grep -Fq '.fi-ta-header-cell' "$repo_dir/ripe/resources/css/_data.css"
grep -Fq '.fi-no-notification' "$repo_dir/ripe/resources/css/_feedback.css"

if rg -n '@import[^;]*https?://' "$repo_dir/ripe/resources/css"; then
    echo 'Ripe runtime CSS must not import remote assets.' >&2
    exit 1
fi

npm --prefix "$repo_dir/ripe" ci
npm --prefix "$repo_dir/ripe" run build
test -s "$repo_dir/ripe/resources/dist/theme.css"

echo 'All theme package checks passed.'
