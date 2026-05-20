#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cd "$APP"
tar xzf ~/payment-gw-deploy.tar.gz -C "$APP"
cp deploy/bootstrap-app-laravel13.php bootstrap/app.php 2>/dev/null || true
$PHP artisan migrate --force
$PHP artisan optimize:clear
echo PAYMENT_GW_OK
