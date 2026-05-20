#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
SRC=~/admin-ui-deploy

cp "$SRC/views/layouts/admin.blade.php" "$APP/resources/views/layouts/"
cp -R "$SRC/views/admin/." "$APP/resources/views/admin/"
cp "$SRC/assist-site.css" "$APP/public_html/assist/assist-site.css"
cp "$SRC/assist-admin.php" "$APP/routes/assist-admin.php"
cp "$SRC/controllers/"*.php "$APP/app/Http/Controllers/Admin/"

rm -f "$APP/resources/views/admin/partials/nav.blade.php" 2>/dev/null || true
rm -f "$APP/resources/views/admin/assist-dashboard/users.blade.php" 2>/dev/null || true

cd "$APP"
$PHP artisan view:clear
$PHP artisan route:clear
echo DEPLOY_ADMIN_OK
head -2 resources/views/admin/assist-dashboard/index.blade.php
grep -c assist-admin public_html/assist/assist-site.css
