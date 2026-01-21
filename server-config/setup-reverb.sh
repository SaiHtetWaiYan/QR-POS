#!/bin/bash

# First-time Reverb setup on server
# Run this once: sudo bash setup-reverb.sh

set -e

echo "=========================================="
echo "  Setting up Laravel Reverb"
echo "=========================================="

# Copy supervisor config
echo "[1/4] Installing Supervisor config..."
cp /var/www/qr-pos/server-config/reverb.conf /etc/supervisor/conf.d/reverb.conf

# Reload supervisor
echo "[2/4] Reloading Supervisor..."
supervisorctl reread
supervisorctl update

# Remind about nginx
echo "[3/4] Nginx configuration..."
echo ""
echo "Add the following to your Nginx server block:"
echo "(/etc/nginx/sites-available/qr-pos.saihtet.dev)"
echo ""
cat /var/www/qr-pos/server-config/nginx-websocket.conf
echo ""

# Test nginx
echo "[4/4] Testing Nginx config..."
nginx -t

echo ""
echo "=========================================="
echo "  Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Add the Nginx location block shown above"
echo "2. Run: sudo systemctl reload nginx"
echo "3. Run: sudo supervisorctl start reverb"
echo "4. Update your .env with values from server-config/.env.production"
echo ""
