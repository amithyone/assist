#!/bin/bash
# Run on server if install failed on PHP 8.1 check:
#   cd ~ && bash assist-pack/deploy/hostinger-continue.sh

set -e
cd ~
git -C assist-pack pull 2>/dev/null || git clone https://github.com/amithyone/assist.git assist-pack
bash assist-pack/deploy/hostinger-install.sh
