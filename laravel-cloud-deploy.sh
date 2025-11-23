#!/bin/bash

# Laravel Cloud Deploy Script
# Script ini akan dijalankan setelah build selesai

echo "🚀 Starting deployment process..."

# IMPORTANT: Backup existing uploaded files before deployment
# This prevents logo and other uploaded files from being lost
echo "💾 Backing up existing uploaded files..."
BACKUP_DIR="storage/app/backup_$(date +%Y%m%d_%H%M%S)"
if [ -d "storage/app/public/hospitals" ] && [ "$(ls -A storage/app/public/hospitals 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    # Backup existing files (excluding .gitkeep)
    mkdir -p "$BACKUP_DIR"
    find storage/app/public/hospitals -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/" \; 2>/dev/null || true
    if [ "$(ls -A "$BACKUP_DIR" 2>/dev/null)" ]; then
        echo "✅ Backup created in $BACKUP_DIR"
    else
        echo "ℹ️  No files to backup (only .gitkeep exists)"
        rmdir "$BACKUP_DIR" 2>/dev/null || true
        BACKUP_DIR=""
    fi
else
    echo "ℹ️  No existing files to backup"
    BACKUP_DIR=""
fi

# Ensure required storage directories exist and are writable
echo "📁 Creating storage directories..."
mkdir -p storage/app/public/hospitals
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage bootstrap/cache

# Restore backed up files if they exist
echo "📥 Restoring backed up files..."
if [ -n "$BACKUP_DIR" ] && [ -d "$BACKUP_DIR" ] && [ "$(ls -A "$BACKUP_DIR" 2>/dev/null)" ]; then
    # Restore files that were backed up
    restored_count=0
    for file in "$BACKUP_DIR"/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            if [ ! -f "storage/app/public/hospitals/$filename" ]; then
                cp "$file" "storage/app/public/hospitals/" 2>/dev/null && restored_count=$((restored_count + 1)) || true
            fi
        fi
    done
    if [ $restored_count -gt 0 ]; then
        echo "✅ Restored $restored_count file(s)"
    else
        echo "ℹ️  All files already exist, no restore needed"
    fi
    # Cleanup backup (keep latest backup for safety)
    # Remove backups older than 7 days
    find storage/app -type d -name "backup_*" -mtime +7 -exec rm -rf {} \; 2>/dev/null || true
else
    echo "ℹ️  No backup to restore"
fi

# Ensure .gitkeep exists (prevents folder from being deleted by git clean)
touch storage/app/public/hospitals/.gitkeep

# Create storage link (only if it doesn't exist)
echo "🔗 Creating storage link..."
if [ ! -L "public/storage" ]; then
    php artisan storage:link
else
    echo "ℹ️  Storage link already exists"
fi

# Clear and optimize
echo "⚙️  Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment completed!"

