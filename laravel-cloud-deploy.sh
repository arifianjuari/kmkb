#!/bin/bash

# Laravel Cloud Deploy Script
# Script ini akan dijalankan setelah build selesai

echo "🚀 Starting deployment process..."

# Run migrations (uncomment jika ingin auto-migrate)
# echo "📊 Running database migrations..."
# php artisan migrate --force

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Clear and optimize
echo "⚙️  Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed!"

