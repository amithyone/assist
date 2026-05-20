#!/bin/bash
set -e
APP=~/domains/assist.amithyone.com
PHP=/opt/alt/php84/usr/bin/php
cd "$APP"
tar xzf ~/site-images-fix.tar.gz -C "$APP"
$PHP artisan assist:publish-site-media
$PHP artisan optimize:clear
echo SITE_IMAGES_OK
