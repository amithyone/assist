#!/bin/bash
# Run ON Hostinger after: cd ~/assist-pack && git pull origin main
set -euo pipefail
APP="${ASSIST_APP:-$HOME/domains/assist.amithyone.com}"
PHP="${ASSIST_PHP:-/opt/alt/php84/usr/bin/php}"
PACK="${ASSIST_PACK:-$HOME/assist-pack}"

if [ ! -d "$PACK" ]; then
  echo "Missing $PACK — run: git clone https://github.com/amithyone/assist.git assist-pack"
  exit 1
fi

cd "$PACK"
git pull origin main

for rel in \
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
  routes/assist-admin.php; do
  mkdir -p "$APP/$(dirname "$rel")"
  cp "$PACK/$rel" "$APP/$rel"
done

cp -R "$PACK/resources/views/admin/assist-plans" "$APP/resources/views/admin/"
cp -R "$PACK/resources/views/admin/assist-vouchers" "$APP/resources/views/admin/"
cp "$PACK/resources/views/admin/assist-dashboard/index.blade.php" "$APP/resources/views/admin/assist-dashboard/"
cp "$PACK/resources/views/admin/partials/sidebar.blade.php" "$APP/resources/views/admin/partials/"
cp "$PACK/resources/views/assist/pricing.blade.php" "$APP/resources/views/assist/"
cp "$PACK/resources/views/assist/billing/payment.blade.php" "$APP/resources/views/assist/billing/"

cd "$APP"
$PHP artisan migrate --force
$PHP artisan view:clear
$PHP artisan route:clear
$PHP artisan optimize:clear
echo PRICING_DEPLOY_OK
echo "Plans admin: https://assist.amithyone.com/admin/assist/plans"
