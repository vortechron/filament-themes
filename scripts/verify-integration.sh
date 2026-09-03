#!/usr/bin/env bash

set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

# Where the throwaway Laravel apps are built.
#
# Not a system temp directory: on some machines a security or cleanup agent
# deletes a freshly created Composer project under /tmp or $TMPDIR partway
# through, which made this script fail with "cd: .../app: No such file or
# directory". A cache directory under $HOME is stable. Override with
# FILAMENT_THEMES_FIXTURE_ROOT if you need somewhere else.
fixture_root="${FILAMENT_THEMES_FIXTURE_ROOT:-$HOME/.cache/filament-themes-integration}"
mkdir -p "$fixture_root"
fixture_dir="$(mktemp -d "$fixture_root/run.XXXXXX")"

# Visual themes to install, as "directory:ClassPrefix". Adding a theme means
# adding one line here and nothing else in this file.
VISUAL_THEMES=(
    'boron:Boron'
    'ripe:Ripe'
    'material:Material'
)

cleanup() {
    # Only ever delete a run directory this script created inside fixture_root.
    case "$fixture_dir" in
        "$fixture_root"/run.??????) rm -rf "$fixture_dir" ;;
        *) echo "Refusing to remove unexpected fixture path: $fixture_dir" >&2 ;;
    esac
}

trap cleanup EXIT

# Every theme gets its own clean Laravel application. Nothing is uninstalled or
# swapped, so one theme can never leave state behind for the next one.
new_laravel_app() {
    app_dir="$fixture_dir/$1"

    composer create-project laravel/laravel:^12.0 "$app_dir" \
        --no-interaction --prefer-dist --quiet
    test -d "$app_dir" || {
        echo "Failed to create the Laravel fixture at $app_dir." >&2
        echo "Set FILAMENT_THEMES_FIXTURE_ROOT to a writable directory and retry." >&2
        exit 1
    }
}

write_panel_provider() {
    php -r '$source = file_get_contents($argv[1]); $source = str_replace("__PLUGIN_CLASS__", $argv[3], $source); file_put_contents($argv[2], $source);' \
        "$repo_dir/tests/integration/AdminPanelProvider.php.stub" \
        "$1/app/Providers/Filament/AdminPanelProvider.php" \
        "$2"
}

install_theme() {
    directory="$1"
    class="$2"
    package="vortechron/filament-$directory"
    plugin_class="Vortechron\\Filament${class}\\${class}Plugin"

    echo "==> $package in a clean Laravel 12 app"
    new_laravel_app "$directory"

    (
        cd "$app_dir"

        composer config repositories.theme path "$repo_dir/$directory"
        COMPOSER_MIRROR_PATH_REPOS=1 composer require "$package:@dev" \
            --no-interaction --prefer-dist --quiet

        php artisan filament:install --panels --no-interaction >/dev/null
        write_panel_provider "$app_dir" "$plugin_class"

        cp "$repo_dir/tests/integration/ThemePackageSmokeTest.php" \
            "$app_dir/tests/Feature/ThemePackageSmokeTest.php"

        php artisan filament:assets >/dev/null
        php artisan route:list --path=admin >/dev/null

        # The publish stub must land where the README says it does.
        php artisan vendor:publish --tag="filament-$directory-theme" \
            --force --no-interaction >/dev/null
        test -s "$app_dir/resources/css/filament/$directory/theme.css"
        test -s "$app_dir/resources/css/filament/$directory/$directory.css"

        EXPECTED_THEME_ASSET="$package" \
            php artisan test tests/Feature/ThemePackageSmokeTest.php --compact
    )
}

for theme_entry in "${VISUAL_THEMES[@]}"; do
    install_theme "${theme_entry%%:*}" "${theme_entry##*:}"
done

echo '==> vortechron/filament-hasnayeen in a clean Laravel 12 app'
new_laravel_app 'hasnayeen'

(
    cd "$app_dir"

    composer config repositories.theme path "$repo_dir/hasnayeen"
    COMPOSER_MIRROR_PATH_REPOS=1 composer require vortechron/filament-hasnayeen:@dev \
        --no-interaction --prefer-dist --quiet

    php artisan filament:install --panels --no-interaction >/dev/null
    write_panel_provider "$app_dir" 'Vortechron\FilamentHasnayeen\HasnayeenPlugin'

    cp "$repo_dir/tests/integration/FilamentUser.php" "$app_dir/app/Models/User.php"
    cp "$repo_dir/tests/integration/HasnayeenSmokeTest.php" \
        "$app_dir/tests/Feature/HasnayeenSmokeTest.php"

    php artisan filament-hasnayeen:install --no-interaction >/dev/null
    php artisan migrate --force >/dev/null
    php artisan filament:assets >/dev/null
    php artisan test tests/Feature/HasnayeenSmokeTest.php --compact
)

echo 'All clean Laravel and Filament integration checks passed.'
