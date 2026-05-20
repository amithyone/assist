#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cp ~/bootstrap-app-laravel13.php "$APP/bootstrap/app.php"
chmod 644 "$APP/bootstrap/app.php"
cd "$APP"
chmod -R ug+rwx storage bootstrap/cache
$PHP artisan config:clear 2>/dev/null || true
$PHP artisan route:clear 2>/dev/null || true
$PHP artisan --version
$PHP artisan route:list --path=assist/setup 2>&1 | head -5
CODE=$(curl -s -o /dev/null -w '%{http_code}' https://assist.amithyone.com/assist/setup)
echo "HTTP setup: $CODE"
curl -s https://assist.amithyone.com/assist/setup 2>&1 | head -3 | wc -c
echo FIX_OK
