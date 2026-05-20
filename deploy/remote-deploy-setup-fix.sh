#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PACK=~/assist-pack
PHP=/opt/alt/php84/usr/bin/php

cd "$PACK" && git pull 2>/dev/null || true

for d in app config database routes resources; do
  cp -R "$PACK/$d/." "$APP/$d/" 2>/dev/null || true
done

PROVIDERS="$APP/bootstrap/providers.php"
if [ -f "$PROVIDERS" ] && ! grep -q AssistServiceProvider "$PROVIDERS"; then
  sed -i.bak "s/];/    App\\Providers\\AssistServiceProvider::class,\n];/" "$PROVIDERS" 2>/dev/null || \
  echo "App\Providers\AssistServiceProvider::class," >> "$PROVIDERS"
fi

cd "$APP"
# Use file sessions until install completes
grep -q '^SESSION_DRIVER=' .env && sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' .env || echo 'SESSION_DRIVER=file' >> .env
rm -f storage/app/.assist-installed 2>/dev/null || true

$PHP artisan config:clear
$PHP artisan route:clear

# If DB is configured, run fresh migrations
if grep -q '^DB_DATABASE=.' .env && ! grep -q '^DB_DATABASE=$' .env; then
  $PHP artisan migrate:fresh --force 2>&1 | tail -20
  $PHP artisan db:seed --class=AssistPlanSeeder --force 2>&1 | tail -5
fi

curl -sI https://assist.amithyone.com/assist/setup | head -4
echo DEPLOY_OK
