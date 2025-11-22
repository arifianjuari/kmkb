#!/bin/bash

# Laravel Cloud Deploy Script
# Script ini akan dijalankan setelah build selesai

echo "🚀 Starting deployment process..."

# Ensure required storage directories exist and are writable
echo "📁 Creating storage directories..."
mkdir -p storage/app/public/hospitals
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Clear and optimize
echo "⚙️  Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed!"

