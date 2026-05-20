#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cp ~/views-sync/assist-web.php "$APP/routes/"
cp -R ~/views-sync/components "$APP/resources/views/"
cp -R ~/views-sync/assist "$APP/resources/views/"
cd "$APP"
$PHP artisan route:clear
$PHP artisan view:clear
$PHP artisan route:list --name=login 2>&1 | head -4
$PHP artisan route:list --name=assist.login 2>&1 | head -4
curl -sI https://assist.amithyone.com/ | head -6
echo SYNC_OK
