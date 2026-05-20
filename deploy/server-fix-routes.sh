#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cp ~/login-fix2/assist-web.php "$APP/routes/" 2>/dev/null || cp ~/views-sync/assist-web.php "$APP/routes/"
cp ~/login-fix2/AssistServiceProvider.php "$APP/app/Providers/"
cd "$APP"
$PHP artisan route:clear
$PHP artisan view:clear
$PHP artisan config:clear
echo "=== login ==="
$PHP artisan route:list --name=login 2>&1 | head -3
echo "=== assist.login ==="
$PHP artisan route:list --name=assist.login 2>&1 | head -3
curl -sI https://assist.amithyone.com/ | head -5
echo ROUTES_OK
