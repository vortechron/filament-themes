#!/usr/bin/env bash

set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
fixture_dir="$(mktemp -d /tmp/filament-themes-integration.XXXXXX)"
app_dir="$fixture_dir/app"

cleanup() {
    case "$fixture_dir" in
        /tmp/filament-themes-integration.*) rm -rf "$fixture_dir" ;;
        *) echo "Refusing to remove unexpected fixture path: $fixture_dir" >&2 ;;
    esac
}

trap cleanup EXIT

composer create-project laravel/laravel:^12.0 "$app_dir" \
    --no-interaction --prefer-dist --quiet

install_theme() {
    directory="$1"
    package="$2"
    plugin_class="$3"
    asset_package="$4"

    (
        cd "$app_dir"

        if [ -d "$app_dir/app/Providers/Filament" ]; then
            php -r '$source = file_get_contents($argv[1]); $source = str_replace("__PLUGIN_CLASS__", $argv[3], $source); file_put_contents($argv[2], $source);' \
                "$repo_dir/tests/integration/AdminPanelProvider.php.stub" \
                "$app_dir/app/Providers/Filament/AdminPanelProvider.php" \
                "$plugin_class"
        fi

        if composer show vortechron/filament-boron >/dev/null 2>&1; then
            composer remove vortechron/filament-boron --no-interaction --no-scripts --quiet
        elif composer show vortechron/filament-ripe >/dev/null 2>&1; then
            composer remove vortechron/filament-ripe --no-interaction --no-scripts --quiet
        fi

        composer config repositories.theme path "$repo_dir/$directory"
        COMPOSER_MIRROR_PATH_REPOS=1 composer require "$package:@dev" \
            --no-interaction --prefer-dist --quiet

        if [ ! -d "$app_dir/app/Providers/Filament" ]; then
            php artisan filament:install --panels --no-interaction >/dev/null
        fi

        php -r '$source = file_get_contents($argv[1]); $source = str_replace("__PLUGIN_CLASS__", $argv[3], $source); file_put_contents($argv[2], $source);' \
            "$repo_dir/tests/integration/AdminPanelProvider.php.stub" \
            "$app_dir/app/Providers/Filament/AdminPanelProvider.php" \
            "$plugin_class"

        cp "$repo_dir/tests/integration/ThemePackageSmokeTest.php" \
            "$app_dir/tests/Feature/ThemePackageSmokeTest.php"

        php artisan filament:assets >/dev/null
        php artisan route:list --path=admin >/dev/null
        EXPECTED_THEME_ASSET="$asset_package" \
            php artisan test tests/Feature/ThemePackageSmokeTest.php --compact
    )
}

install_theme 'boron' 'vortechron/filament-boron' \
    'Vortechron\FilamentBoron\BoronPlugin' 'vortechron/filament-boron'
install_theme 'ripe' 'vortechron/filament-ripe' \
    'Vortechron\FilamentRipe\RipePlugin' 'vortechron/filament-ripe'

(
    cd "$app_dir"
    php -r '$source = file_get_contents($argv[1]); $source = str_replace("__PLUGIN_CLASS__", $argv[3], $source); file_put_contents($argv[2], $source);' \
        "$repo_dir/tests/integration/AdminPanelProvider.php.stub" \
        "$app_dir/app/Providers/Filament/AdminPanelProvider.php" \
        'Vortechron\FilamentHasnayeen\HasnayeenPlugin'
    composer remove vortechron/filament-ripe --no-interaction --no-scripts --quiet
    composer config repositories.theme path "$repo_dir/hasnayeen"
    COMPOSER_MIRROR_PATH_REPOS=1 composer require vortechron/filament-hasnayeen:@dev \
        --no-interaction --prefer-dist --quiet

    php -r '$source = file_get_contents($argv[1]); $source = str_replace("__PLUGIN_CLASS__", $argv[3], $source); file_put_contents($argv[2], $source);' \
        "$repo_dir/tests/integration/AdminPanelProvider.php.stub" \
        "$app_dir/app/Providers/Filament/AdminPanelProvider.php" \
        'Vortechron\FilamentHasnayeen\HasnayeenPlugin'
    cp "$repo_dir/tests/integration/FilamentUser.php" "$app_dir/app/Models/User.php"
    cp "$repo_dir/tests/integration/HasnayeenSmokeTest.php" \
        "$app_dir/tests/Feature/HasnayeenSmokeTest.php"

    php artisan filament-hasnayeen:install --no-interaction >/dev/null
    php artisan migrate --force >/dev/null
    php artisan filament:assets >/dev/null
    php artisan test tests/Feature/HasnayeenSmokeTest.php --compact
)

echo 'All clean Laravel and Filament integration checks passed.'
