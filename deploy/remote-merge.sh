#!/bin/bash
set -e
SITE=~/domains/amithyone.com/public_html/assist-site
PACK=~/assist-pack
cd "$SITE"

for d in app config database routes resources; do
  cp -R "$PACK/$d/." "./$d/" 2>/dev/null || true
done
mkdir -p public/assist
cp -R "$PACK/public/assist/." public/assist/ 2>/dev/null || true

if ! grep -q assist-setup.php routes/web.php; then
  cat >> routes/web.php << 'EOF'

// Assist integration
require base_path('routes/assist-setup.php');
require base_path('routes/assist-web.php');
Route::middleware(['auth', 'assist.admin'])->prefix('admin/assist')->group(function () {
    require base_path('routes/assist-admin.php');
});
EOF
fi

if [ -f routes/api.php ] && ! grep -q assist-api.php routes/api.php; then
  echo "require base_path('routes/assist-api.php');" >> routes/api.php
fi

if [ -f bootstrap/app.php ] && ! grep -q assist.setup bootstrap/app.php; then
  php -r '
$f = "bootstrap/app.php";
$c = file_get_contents($f);
$snippet = "        ->withMiddleware(function (Middleware \$middleware) {\n            \$middleware->alias([\n                \"assist.key\" => \\App\\Http\\Middleware\\AssistApiKey::class,\n                \"assist.setup\" => \\App\\Http\\Middleware\\AssistSetupGate::class,\n                \"assist.admin\" => \\App\\Http\\Middleware\\EnsureAssistAdmin::class,\n            ]);\n        })";
if (strpos($c, "assist.setup") === false && preg_match("/->withMiddleware\\(function \\(Middleware/", $c)) {
  echo "middleware block exists - manual check needed\n";
} else {
  echo "check bootstrap manually\n";
}
'
fi

composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
chmod -R ug+rwx storage bootstrap/cache
echo "MERGE_OK"
