#!/bin/bash

# Laravel Cloud Build Script
# Script ini akan dijalankan otomatis oleh Laravel Cloud saat deploy

echo "🚀 Starting build process..."

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Install NPM dependencies
echo "📦 Installing NPM dependencies..."
npm ci

# Build frontend assets
echo "🎨 Building frontend assets..."
npm run build

# Clear and cache configuration
echo "⚙️  Optimizing application..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Build process completed!"

