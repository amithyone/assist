#!/bin/bash
# Deploy pricing + vouchers from Mac to Hostinger (assist.amithyone.com).
# Usage: bash assist-integration/deploy/local-deploy-pricing.sh
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
SSH_HOST="${ASSIST_SSH_HOST:-u429468666@77.37.37.190}"
SSH_PORT="${ASSIST_SSH_PORT:-65002}"
APP="${ASSIST_APP_DIR:-domains/assist.amithyone.com}"
PHP="${ASSIST_PHP:-/opt/alt/php84/usr/bin/php}"
TAR="/tmp/assist-pricing-deploy.tar.gz"

echo "==> Packing assist-integration pricing files"
cd "$ROOT"
tar czf "$TAR" \
  app/Http/Controllers/Admin/AssistPlansAdminController.php \
  app/Http/Controllers/Admin/AssistVouchersAdminController.php \
  app/Http/Controllers/Web/AssistBillingController.php \
  app/Models/Voucher.php \
  app/Models/Plan.php \
  app/Models/Payment.php \
  app/Services/VoucherService.php \
  app/Services/CheckoutPayService.php \
  app/Services/PaystackService.php \
  app/Services/PaymentGatewayManager.php \
  app/Services/PaymentActivationService.php \
  database/migrations/2026_05_21_000002_add_is_featured_to_plans_table.php \
  database/migrations/2026_05_21_000003_create_vouchers_table.php \
  database/migrations/2026_05_21_000004_add_voucher_fields_to_payments_table.php \
  database/seeders/AssistPlanSeeder.php \
  routes/assist-admin.php \
  resources/views/admin/assist-plans \
  resources/views/admin/assist-vouchers \
  resources/views/admin/assist-dashboard/index.blade.php \
  resources/views/admin/partials/sidebar.blade.php \
  resources/views/assist/pricing.blade.php \
  resources/views/assist/billing/payment.blade.php \
  tests/Feature/VoucherServiceTest.php

echo "==> Uploading to server"
scp -P "$SSH_PORT" -o StrictHostKeyChecking=accept-new "$TAR" "$SSH_HOST:~/assist-pricing-deploy.tar.gz"

echo "==> Applying on server (extract + migrate + cache clear)"
ssh -p "$SSH_PORT" -o StrictHostKeyChecking=accept-new "$SSH_HOST" bash -s <<REMOTE
set -e
APP="\$HOME/$APP"
PHP="$PHP"
cd "\$APP"
tar xzf ~/assist-pricing-deploy.tar.gz -C "\$APP"
\$PHP artisan migrate --force
\$PHP artisan view:clear
\$PHP artisan route:clear
\$PHP artisan optimize:clear
echo PRICING_DEPLOY_OK
REMOTE

rm -f "$TAR"
echo "Done. Admin: https://assist.amithyone.com/admin/assist/plans"
