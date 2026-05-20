#!/bin/bash
# Build a Laravel+Assist release zip with vendor/ for shared hosts without SSH composer.
# Run on your Mac/dev machine (not on Hostinger):
#   cd assist-integration/deploy && bash build-release.sh

set -e
ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
OUT="${OUT:-$ROOT/assist-integration/deploy/dist}"
STAGING="$OUT/laravel-assist-staging"
ZIP="$OUT/assist-laravel-vendor.zip"

PHP_BIN="${PHP_BIN:-php}"
COMPOSER="${COMPOSER:-composer}"

echo "==> Staging Laravel + Assist pack"
rm -rf "$STAGING" "$ZIP"
mkdir -p "$STAGING"

$COMPOSER create-project laravel/laravel "$STAGING" --no-interaction --prefer-dist
cd "$STAGING"
$COMPOSER require laravel/sanctum --no-interaction
$COMPOSER install --no-dev --optimize-autoloader --no-interaction

PACK="$ROOT/assist-integration"
for dir in app config database routes resources public; do
  [ -d "$PACK/$dir" ] && cp -R "$PACK/$dir/." "$STAGING/$dir/"
done

echo "==> Zipping (includes vendor/)"
mkdir -p "$(dirname "$ZIP")"
cd "$STAGING"
zip -rq "$ZIP" . -x "*.git*"
echo "Created: $ZIP"
echo "Upload and extract into your domain folder (parent of public_html), then run /assist/setup for DB/mail/admin only."
