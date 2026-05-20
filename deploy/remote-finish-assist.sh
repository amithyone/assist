#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cd "$APP"

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

if ! grep -q assist-api.php routes/api.php; then
  echo "require base_path('routes/assist-api.php');" >> routes/api.php
fi

# Laravel 11+ middleware in bootstrap/app.php
if [ -f bootstrap/app.php ] && ! grep -q AssistSetupGate bootstrap/app.php; then
  $PHP -r '
$f = "bootstrap/app.php";
$c = file_get_contents($f);
$snippet = "
    \$middleware->alias([
        \"assist.key\" => \\App\\Http\\Middleware\\AssistApiKey::class,
        \"assist.setup\" => \\App\\Http\\Middleware\\AssistSetupGate::class,
        \"assist.admin\" => \\App\\Http\\Middleware\\EnsureAssistAdmin::class,
    ]);";
if (strpos($c, "AssistSetupGate") !== false) exit(0);
if (preg_match("/->withMiddleware\\(\\s*function\\s*\\(\\s*\\\\?Illuminate\\\\Foundation\\\\Configuration\\\\Middleware\\s*\\\$middleware\\s*\\)\\s*\\{/", $c)) {
  $c = preg_replace(
    "/(->withMiddleware\\(\\s*function\\s*\\(\\s*\\\\?Illuminate\\\\Foundation\\\\Configuration\\\\Middleware\\s*\\\$middleware\\s*\\)\\s*\\{)/",
    "$1".$snippet,
    $c,
    1
  );
  file_put_contents($f, $c);
  echo "middleware added\n";
} else {
  echo "manual middleware needed\n";
}
'
fi

chmod -R ug+rwx storage bootstrap/cache
$PHP artisan route:list --path=assist 2>&1 | head -25
echo "FINISH_OK"
