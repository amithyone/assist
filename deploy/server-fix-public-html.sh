#!/bin/bash
# Restore assist.amithyone.com public_html (Laravel front controller + assets).
set -euo pipefail

APP="${ASSIST_APP:-$HOME/domains/assist.amithyone.com}"
PUBLIC_HTML="$APP/public_html"
PACK="${ASSIST_PACK:-$HOME/assist-pack}"
PHP="${ASSIST_PHP:-/opt/alt/php84/usr/bin/php}"
TS=$(date +%Y%m%d%H%M%S)

if [ ! -f "$APP/artisan" ]; then
  echo "ERROR: Laravel app missing at $APP"
  exit 1
fi

if [ -f "$PUBLIC_HTML/index.php" ] && [ ! -d "$PUBLIC_HTML/.git" ] && [ ! -d "$PUBLIC_HTML/app" ]; then
  echo "public_html already OK — nothing to fix"
  exit 0
fi

echo "==> Backing up broken public_html"
mv "$PUBLIC_HTML" "${APP}/public_html.broken-${TS}"
mkdir -p "$PUBLIC_HTML"

echo "==> Writing Laravel index.php and .htaccess"
cat > "$PUBLIC_HTML/index.php" <<'EOF'
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
EOF

cat > "$PUBLIC_HTML/.htaccess" <<'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF

echo "==> Copying static assets"
mkdir -p "$PUBLIC_HTML/assist"
if [ -d "$APP/public/assist" ]; then
  cp -R "$APP/public/assist/." "$PUBLIC_HTML/assist/"
fi
if [ -d "$PACK/public/assist" ]; then
  cp -R "$PACK/public/assist/." "$PUBLIC_HTML/assist/"
fi

if [ -d "$APP/storage/app/public" ]; then
  rm -f "$PUBLIC_HTML/storage"
  ln -sfn "$APP/storage/app/public" "$PUBLIC_HTML/storage"
fi

chmod 644 "$PUBLIC_HTML/index.php" "$PUBLIC_HTML/.htaccess"
echo "Do not git clone or git pull in public_html — Laravel web root only." > "$PUBLIC_HTML/DO_NOT_GIT_PULL_HERE.txt"
rm -rf "$PUBLIC_HTML/.git" "$PUBLIC_HTML/app" "$PUBLIC_HTML/config" "$PUBLIC_HTML/database" "$PUBLIC_HTML/routes" "$PUBLIC_HTML/resources" "$PUBLIC_HTML/tests" "$PUBLIC_HTML/ui" "$PUBLIC_HTML/deploy" 2>/dev/null || true
chmod -R ug+rwx "$APP/storage" "$APP/bootstrap/cache" 2>/dev/null || true

cd "$APP"
$PHP artisan optimize:clear 2>/dev/null || true

echo PUBLIC_HTML_FIX_OK
ls -la "$PUBLIC_HTML/index.php" "$PUBLIC_HTML/.htaccess" "$PUBLIC_HTML/assist" 2>/dev/null | head -8
