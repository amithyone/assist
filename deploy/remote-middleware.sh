#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cd "$APP"

# Patch bootstrap/app.php middleware block
if ! grep -q AssistSetupGate bootstrap/app.php; then
  $PHP -r '
$f = "bootstrap/app.php";
$c = file_get_contents($f);
$old = "    ->withMiddleware(function (Middleware \$middleware): void {\n        //\n    })";
$new = "    ->withMiddleware(function (Middleware \$middleware): void {\n        \$middleware->alias([\n            \"assist.key\" => \\App\\Http\\Middleware\\AssistApiKey::class,\n            \"assist.setup\" => \\App\\Http\\Middleware\\AssistSetupGate::class,\n            \"assist.admin\" => \\App\\Http\\Middleware\\EnsureAssistAdmin::class,\n        ]);\n        \$middleware->validateCsrfTokens(except: [\n            \"webhooks/checkoutpay\",\n        ]);\n    })";
if (strpos($c, "AssistSetupGate") === false) {
  $c = str_replace($old, $new, $c);
  file_put_contents($f, $c);
  echo "patched bootstrap\n";
}
'
fi

# API routes (Laravel 11+ may need bootstrap routing - add to web or bootstrap)
if [ -f routes/api.php ] && ! grep -q assist-api.php routes/api.php; then
  echo "require base_path('routes/assist-api.php');" >> routes/api.php
fi

# Register api routes in bootstrap if missing
if ! grep -q "routes/api.php" bootstrap/app.php; then
  $PHP -r '
$f = "bootstrap/app.php";
$c = file_get_contents($f);
if (strpos($c, "api.php") === false && strpos($c, "withRouting") !== false) {
  $c = preg_replace(
    "/->withRouting\\(\\s*web: __DIR__\\.\\'\\/\\.\\.\\/routes\\/web\\.php\\',/",
    "->withRouting(\n        web: __DIR__.\"/../routes/web.php\",\n        api: __DIR__.\"/../routes/api.php\",",
    $c,
    1
  );
  file_put_contents($f, $c);
  echo "api routing added\n";
}
' 2>/dev/null || true
fi

$PHP artisan config:clear 2>/dev/null || true
$PHP artisan route:clear 2>/dev/null || true
tail -30 storage/logs/laravel.log 2>/dev/null || echo "no log yet"
echo "MW_OK"
