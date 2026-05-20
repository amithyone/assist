#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cd "$APP"
tar xzf ~/app-api-deploy.tar.gz -C "$APP" 2>/dev/null || true
$PHP artisan optimize:clear
echo APP_API_OK
