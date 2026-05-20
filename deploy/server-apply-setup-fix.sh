#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cp ~/assist-setup-fix/AssistInstallerService.php "$APP/app/Services/"
mkdir -p "$APP/app/Providers"
cp ~/assist-setup-fix/AssistServiceProvider.php "$APP/app/Providers/"
cp ~/assist-setup-fix/AssistSetupController.php "$APP/app/Http/Controllers/Setup/"
grep -q AssistServiceProvider "$APP/bootstrap/providers.php" 2>/dev/null || echo 'App\Providers\AssistServiceProvider::class,' >> "$APP/bootstrap/providers.php"
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' "$APP/.env"
rm -f "$APP/storage/app/.assist-installed"
cd "$APP"
$PHP artisan config:clear
$PHP artisan migrate:fresh --force 2>&1 | tail -12
$PHP artisan db:seed --class=AssistPlanSeeder --force 2>&1
curl -sI https://assist.amithyone.com/assist/setup | head -5
echo OK
