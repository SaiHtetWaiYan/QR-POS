#!/bin/bash

# QR-POS Deployment Script
# Usage: ./deploy.sh

set -e

APP_DIR="/var/www/qr-pos"
BRANCH="main"

echo "=========================================="
echo "  QR-POS Deployment Script"
echo "=========================================="

cd $APP_DIR

# Pull latest code
echo "[1/7] Pulling latest code..."
git pull origin $BRANCH

# Install PHP dependencies
echo "[2/7] Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build
echo "[3/7] Installing NPM dependencies..."
npm ci

echo "[4/7] Building frontend assets..."
npm run build

# Clear and cache Laravel
echo "[5/7] Clearing and caching Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "[6/7] Running migrations..."
php artisan migrate --force

# Restart services
echo "[7/7] Restarting services..."
sudo supervisorctl restart reverb || sudo supervisorctl start reverb
sudo systemctl reload nginx

echo ""
echo "=========================================="
echo "  Deployment Complete!"
echo "=========================================="
echo ""
echo "Verify Reverb: sudo supervisorctl status reverb"
echo "View logs: tail -f /var/log/reverb.log"
