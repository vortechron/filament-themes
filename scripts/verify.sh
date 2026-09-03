#!/usr/bin/env bash

set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Every theme package in the monorepo, as "directory:Namespace:ClassPrefix".
# Add a new theme here first; the loops below derive every check from it.
THEMES=(
    'boron:Vortechron\FilamentBoron:Boron'
    'hasnayeen:Vortechron\FilamentHasnayeen:Hasnayeen'
    'material:Vortechron\FilamentMaterial:Material'
    'ripe:Vortechron\FilamentRipe:Ripe'
)

# Themes that apply one fixed look. These must satisfy the full plugin API
# contract in AGENTS.md. Theme switchers (hasnayeen) are exempt.
VISUAL_THEMES=('boron' 'material' 'ripe')

# Themes that compile resources/css into resources/dist/theme.css with esbuild.
BUILD_THEMES=('material' 'ripe')

fail() {
    echo "verify: $1" >&2
    exit 1
}

theme_dirs() {
    for entry in "${THEMES[@]}"; do
        printf '%s\n' "${entry%%:*}"
    done
}

# --- Composer manifests -----------------------------------------------------

for theme in $(theme_dirs); do
    composer validate --working-dir="$repo_dir/$theme" --strict --no-check-publish
done
composer validate --working-dir="$repo_dir" --strict --no-check-publish

# --- PHP lint and strict types ----------------------------------------------

find $(theme_dirs | sed "s|^|$repo_dir/|") \
    -path '*/vendor' -prune -o \
    -path '*/node_modules' -prune -o \
    -type f -name '*.php' -print0 \
    | xargs -0 -n1 php -l

while IFS= read -r -d '' php_file; do
    grep -Fq 'declare(strict_types=1);' "$php_file" \
        || fail "missing declare(strict_types=1) in $php_file"
done < <(find $(theme_dirs | sed "s|^|$repo_dir/|;s|$|/src|") -type f -name '*.php' -print0)

# --- Naming rules -----------------------------------------------------------

for entry in "${THEMES[@]}"; do
    theme="${entry%%:*}"
    rest="${entry#*:}"
    namespace="${rest%%:*}"
    class="${rest##*:}"
    dir="$repo_dir/$theme"

    test -f "$dir/src/${class}Plugin.php" || fail "$theme: missing src/${class}Plugin.php"
    test -f "$dir/src/${class}ServiceProvider.php" \
        || fail "$theme: missing src/${class}ServiceProvider.php"

    grep -Fq "namespace ${namespace};" "$dir/src/${class}Plugin.php" \
        || fail "$theme: wrong namespace in ${class}Plugin.php"
    grep -Fq "namespace ${namespace};" "$dir/src/${class}ServiceProvider.php" \
        || fail "$theme: wrong namespace in ${class}ServiceProvider.php"

    grep -Fq "public const ID = 'vortechron-${theme}';" "$dir/src/${class}Plugin.php" \
        || fail "$theme: plugin ID must be 'vortechron-${theme}'"
    grep -Fq "public const ASSET_PACKAGE = 'vortechron/filament-${theme}';" \
        "$dir/src/${class}Plugin.php" \
        || fail "$theme: asset package key must be 'vortechron/filament-${theme}'"
    grep -Fq "public static string \$name = 'filament-${theme}';" \
        "$dir/src/${class}ServiceProvider.php" \
        || fail "$theme: spatie package name must be 'filament-${theme}'"

    grep -Fq "\"vortechron/filament-${theme}\"" "$dir/composer.json" \
        || fail "$theme: composer name must be vortechron/filament-${theme}"

    # Registered in the root package too. JSON escapes each backslash.
    json_namespace="$(printf '%s' "$namespace" | sed 's/[\\]/\\\\/g')"
    grep -Fq "${json_namespace}" "$repo_dir/composer.json" \
        || fail "$theme: namespace missing from the root composer.json autoload"
    grep -Fq "${class}ServiceProvider" "$repo_dir/composer.json" \
        || fail "$theme: service provider missing from the root composer.json"

    # Shared repository hygiene.
    test -f "$dir/.gitignore" || fail "$theme: missing .gitignore"
    diff -q "$dir/.gitignore" "$repo_dir/boron/.gitignore" >/dev/null \
        || fail "$theme: .gitignore differs from boron/.gitignore"
    test -s "$dir/LICENSE.md" || fail "$theme: missing LICENSE.md"
    test -s "$dir/README.md" || fail "$theme: missing README.md"
done

# --- Legacy namespaces ------------------------------------------------------

if rg -n '^(namespace|use) (Ripe\\Theme|Hasnayeen\\Themes)' \
    $(theme_dirs | sed "s|^|$repo_dir/|;s|$|/src|"); then
    fail 'legacy package namespace found'
fi

# --- Visual theme plugin API contract ---------------------------------------

for theme in "${VISUAL_THEMES[@]}"; do
    class=''
    for entry in "${THEMES[@]}"; do
        [ "${entry%%:*}" = "$theme" ] || continue
        class="${entry##*:}"
    done
    [ -n "$class" ] || fail "$theme: listed in VISUAL_THEMES but not in THEMES"
    dir="$repo_dir/$theme"
    plugin="$dir/src/${class}Plugin.php"

    test -f "$dir/src/${class}Theme.php" \
        || fail "$theme: visual themes must define src/${class}Theme.php"
    grep -Fq 'public static function colors(): array' "$dir/src/${class}Theme.php" \
        || fail "$theme: ${class}Theme must expose colors(): array"
    grep -Fq "${class}Theme::colors()" "$plugin" \
        || fail "$theme: ${class}Plugin must read its palette from ${class}Theme::colors()"

    for method in withoutColors withoutStyles; do
        grep -Fq "public function ${method}(): static" "$plugin" \
            || fail "$theme: ${class}Plugin must expose ${method}()"
    done

    # withoutFont() is required exactly when the package bundles a font.
    if [ -d "$dir/resources/fonts" ]; then
        grep -Fq 'public function withoutFont(): static' "$plugin" \
            || fail "$theme: bundles a font, so ${class}Plugin must expose withoutFont()"
        test -s "$dir"/resources/fonts/*/index.css \
            || fail "$theme: bundled font is missing index.css"
        test -s "$dir"/resources/fonts/*/LICENSE \
            || fail "$theme: bundled font is missing its LICENSE"
        ls "$dir"/resources/fonts/*/*.woff2 >/dev/null 2>&1 \
            || fail "$theme: bundled font has no woff2 file"
    else
        grep -Fq 'public function withoutFont(): static' "$plugin" \
            && fail "$theme: exposes withoutFont() but bundles no font"
    fi

    # Publishable Vite stub, wired to the standard tag.
    test -s "$dir/resources/stubs/theme.css" || fail "$theme: missing resources/stubs/theme.css"
    grep -Fq "'filament-${theme}-theme'" "$dir/src/${class}ServiceProvider.php" \
        || fail "$theme: service provider must publish under the filament-${theme}-theme tag"
    grep -Fq "resource_path('css/filament/${theme}/theme.css')" \
        "$dir/src/${class}ServiceProvider.php" \
        || fail "$theme: stub must publish to resources/css/filament/${theme}/theme.css"
    grep -Fq "@import './${theme}.css';" "$dir/resources/stubs/theme.css" \
        || fail "$theme: stub must import ./${theme}.css"
done

# --- No remote runtime assets in any theme ----------------------------------

for theme in $(theme_dirs); do
    if [ -d "$repo_dir/$theme/resources/css" ] \
        && rg -n '@import[^;]*https?://' "$repo_dir/$theme/resources/css"; then
        fail "$theme: runtime CSS must not import remote assets"
    fi
    if [ -d "$repo_dir/$theme/resources/dist" ] \
        && rg -n 'https?://fonts\.(googleapis|gstatic)\.com' "$repo_dir/$theme/resources/dist"; then
        fail "$theme: built CSS must not reference a font CDN"
    fi
done

# --- README section order ---------------------------------------------------

php -r '
$required = ["Requirements", "Install", "Register", "Options", "Customize",
             "Package development", "Production check", "License"];
$status = 0;
foreach (array_slice($argv, 1) as $path) {
    preg_match_all("/^## (.+)$/m", file_get_contents($path), $m);
    $found = $m[1];
    $cursor = 0;
    foreach ($required as $heading) {
        $at = array_search($heading, array_slice($found, $cursor), true);
        if ($at === false) {
            fwrite(STDERR, "verify: $path is missing or misorders \"## $heading\"\n");
            $status = 1;
            continue 2;
        }
        $cursor += $at + 1;
    }
}
exit($status);
' $(theme_dirs | sed "s|^|$repo_dir/|;s|$|/README.md|")

# British spellings the style guide rejects.
if rg -n --pcre2 '\b(customise|customised|colour|colours|licence|recognisable)\b' -i \
    $(theme_dirs | sed "s|^|$repo_dir/|;s|$|/README.md|") "$repo_dir/README.md"; then
    fail 'READMEs must use American spelling (color, customize, license)'
fi

# The root README must name every theme.
for theme in $(theme_dirs); do
    grep -Fq "vortechron/filament-${theme}" "$repo_dir/README.md" \
        || fail "root README does not mention vortechron/filament-${theme}"
done

# --- Required assets --------------------------------------------------------

test -s "$repo_dir/boron/resources/css/theme.css"
test -s "$repo_dir/boron/resources/fonts/lexend/index.css"
test -s "$repo_dir/boron/resources/fonts/lexend/lexend-latin-wght-normal.woff2"
test -s "$repo_dir/boron/resources/fonts/lexend/LICENSE"

for hasnayeen_style in appearance default dracula nord sunset; do
    test -s "$repo_dir/hasnayeen/resources/dist/${hasnayeen_style}.css"
done

test -s "$repo_dir/material/resources/fonts/roboto/index.css"
test -s "$repo_dir/material/resources/fonts/roboto/roboto-latin-wght-normal.woff2"
test -s "$repo_dir/material/resources/fonts/roboto/LICENSE"

test -s "$repo_dir/ripe/resources/fonts/inter/index.css"
test -s "$repo_dir/ripe/resources/fonts/inter/inter-latin-wght-normal.woff2"
test -s "$repo_dir/ripe/resources/fonts/inter/LICENSE"

# --- Build-step themes share the same CSS module layout ---------------------

for theme in "${BUILD_THEMES[@]}"; do
    for module in \
        _tokens.css \
        _foundations.css \
        _navigation.css \
        _components.css \
        _forms.css \
        _data.css \
        _feedback.css \
        _patterns.css \
        _dark.css \
        _overrides.css \
        dist-entry.css; do
        test -s "$repo_dir/$theme/resources/css/$module" \
            || fail "$theme: missing resources/css/$module"
    done

    for script in build build:pretty dev; do
        grep -Fq "\"$script\":" "$repo_dir/$theme/package.json" \
            || fail "$theme: package.json is missing the \"$script\" script"
    done
done

# --- Theme token values stay in sync with the PHP palette -------------------

grep -Fq -- '--md-primary: #6750a4;' "$repo_dir/material/resources/css/_tokens.css"
grep -Fq -- '--md-primary: #d0bcff;' "$repo_dir/material/resources/css/_dark.css"
grep -Fq "'primary' => '#6750a4'," "$repo_dir/material/src/MaterialTheme.php"

grep -Fq -- '--ripe-primary: #533afd;' "$repo_dir/ripe/resources/css/_tokens.css"
grep -Fq "'primary' => '#533afd'," "$repo_dir/ripe/src/RipeTheme.php"

grep -Fq "'primary' => '#313a46'," "$repo_dir/boron/src/BoronTheme.php"

# --- Filament 5 CSS hooks ---------------------------------------------------

grep -Fq '.fi-btn.fi-color-primary' "$repo_dir/material/resources/css/_components.css"
grep -Fq '.fi-tabs-item.fi-active' "$repo_dir/material/resources/css/_navigation.css"
grep -Fq '.fi-sidebar-item.fi-active .fi-sidebar-item-btn' \
    "$repo_dir/material/resources/css/_navigation.css"
grep -Fq '.fi-input-wrp.fi-invalid' "$repo_dir/material/resources/css/_forms.css"
grep -Fq '.fi-ta-header-cell' "$repo_dir/material/resources/css/_data.css"
grep -Fq '.fi-no-notification' "$repo_dir/material/resources/css/_feedback.css"

grep -Fq '.fi-btn.fi-color-primary' "$repo_dir/ripe/resources/css/_components.css"
grep -Fq '.fi-tabs-item.fi-active' "$repo_dir/ripe/resources/css/_components.css"
grep -Fq '.fi-input-wrp.fi-invalid' "$repo_dir/ripe/resources/css/_forms.css"
grep -Fq '.fi-ta-header-cell' "$repo_dir/ripe/resources/css/_data.css"
grep -Fq '.fi-no-notification' "$repo_dir/ripe/resources/css/_feedback.css"

test -s "$repo_dir/ripe/STRIPE_APPS_V9_COVERAGE.md"

# --- Rebuild and confirm the committed stylesheets are current --------------

for theme in "${BUILD_THEMES[@]}"; do
    dist="$repo_dir/$theme/resources/dist/theme.css"
    before="$(md5 -q "$dist" 2>/dev/null || md5sum "$dist" | cut -d' ' -f1)"
    npm --prefix "$repo_dir/$theme" ci
    npm --prefix "$repo_dir/$theme" run build
    test -s "$dist" || fail "$theme: build produced no resources/dist/theme.css"
    after="$(md5 -q "$dist" 2>/dev/null || md5sum "$dist" | cut -d' ' -f1)"
    [ "$before" = "$after" ] \
        || fail "$theme: committed resources/dist/theme.css was stale; rebuilt, commit it"
done

echo 'All theme package checks passed.'
