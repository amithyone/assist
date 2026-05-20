#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
PACK=~/assist-pack

# Sync route + middleware + bootstrap fix
cp "$PACK/routes/assist-web.php" "$APP/routes/" 2>/dev/null || true
grep -q "redirectGuestsTo" "$APP/bootstrap/app.php" || sed -i "/validateCsrfTokens/i\\        \$middleware->redirectGuestsTo('/login');" "$APP/bootstrap/app.php"

# Ensure login route name in assist-web
sed -i "s/->name('assist.login')/->name('login')/" "$APP/routes/assist-web.php"

cd "$APP"
$PHP artisan route:clear
$PHP artisan config:clear
$PHP artisan route:list --name=login 2>&1 | head -5
curl -sI https://assist.amithyone.com/admin/assist 2>&1 | head -8
echo LOGIN_FIX_OK
