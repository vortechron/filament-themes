#!/usr/bin/env bash

set -euo pipefail

repo_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"

for package_dir in "$repo_dir" "$repo_dir/boron" "$repo_dir/hasnayeen" "$repo_dir/ripe"; do
    composer validate --working-dir="$package_dir" --strict --no-check-publish
done

find "$repo_dir/boron" "$repo_dir/hasnayeen" "$repo_dir/ripe" \
    -path '*/vendor' -prune -o \
    -path '*/node_modules' -prune -o \
    -type f -name '*.php' -print0 \
    | xargs -0 -n1 php -l

test -s "$repo_dir/boron/resources/css/boron.css"
test -s "$repo_dir/hasnayeen/resources/dist/themes.css"
test -s "$repo_dir/hasnayeen/resources/dist/default.css"
test -s "$repo_dir/hasnayeen/resources/dist/dracula.css"
test -s "$repo_dir/hasnayeen/resources/dist/nord.css"
test -s "$repo_dir/hasnayeen/resources/dist/sunset.css"

npm --prefix "$repo_dir/ripe" ci
npm --prefix "$repo_dir/ripe" run build
test -s "$repo_dir/ripe/dist/theme.css"

echo 'All theme package checks passed.'

