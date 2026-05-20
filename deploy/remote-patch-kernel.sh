#!/bin/bash
set -e
SITE=~/domains/amithyone.com/public_html/assist-site
cd "$SITE"

KERNEL=app/Http/Kernel.php
if ! grep -q 'assist.key' "$KERNEL"; then
  sed -i.bak "/'verified' =>/a\\
        'assist.key' => \\\\App\\\\Http\\\\Middleware\\\\AssistApiKey::class,\\
        'assist.setup' => \\\\App\\\\Http\\\\Middleware\\\\AssistSetupGate::class,\\
        'assist.admin' => \\\\App\\\\Http\\\\Middleware\\\\EnsureAssistAdmin::class," "$KERNEL"
fi

CSRF=app/Http/Middleware/VerifyCsrfToken.php
if ! grep -q checkoutpay "$CSRF" 2>/dev/null; then
  if grep -q 'protected \$except' "$CSRF"; then
    sed -i.bak "s/protected \\\$except = \[\]/protected \\\$except = [\n        'webhooks\/checkoutpay',\n    ]/" "$CSRF" || true
  fi
fi

php artisan route:list --path=assist 2>&1 | head -25 || true
echo "KERNEL_PATCH_OK"
