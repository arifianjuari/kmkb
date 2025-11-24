#!/bin/bash

# Laravel Cloud Deployment Hooks
# Script ini untuk digunakan di Laravel Cloud Deployment Hooks

# ============================================
# BEFORE DEPLOY HOOK
# ============================================
# Copy script ini ke "Before Deploy" hook di Laravel Cloud

# Backup existing uploaded files to persistent location
# Using /tmp might not work, so we use a location outside project directory
BACKUP_BASE="/home/forge/storage_backup"
BACKUP_DIR="$BACKUP_BASE/hospitals_$(date +%Y%m%d_%H%M%S)"

if [ -d "storage/app/public/hospitals" ] && [ "$(ls -A storage/app/public/hospitals 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up hospital logos..."
    mkdir -p "$BACKUP_DIR"
    find storage/app/public/hospitals -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/" \; 2>/dev/null
    if [ "$(ls -A "$BACKUP_DIR" 2>/dev/null)" ]; then
        echo "✅ Backup created: $BACKUP_DIR"
        # Keep only last 3 backups
        ls -dt "$BACKUP_BASE"/hospitals_* 2>/dev/null | tail -n +4 | xargs rm -rf 2>/dev/null || true
    else
        echo "ℹ️  No files to backup"
        rmdir "$BACKUP_DIR" 2>/dev/null || true
    fi
else
    echo "ℹ️  No existing files to backup"
fi

# ============================================
# AFTER DEPLOY HOOK  
# ============================================
# Copy script ini ke "After Deploy" hook di Laravel Cloud

# Restore backed up files
echo "📥 Restoring hospital logos..."
mkdir -p storage/app/public/hospitals

# Find latest backup
LATEST_BACKUP=$(ls -dt "$BACKUP_BASE"/hospitals_* 2>/dev/null | head -1)

if [ -n "$LATEST_BACKUP" ] && [ -d "$LATEST_BACKUP" ] && [ "$(ls -A "$LATEST_BACKUP" 2>/dev/null)" ]; then
    restored_count=0
    for file in "$LATEST_BACKUP"/*; do
        if [ -f "$file" ]; then
            filename=$(basename "$file")
            if [ ! -f "storage/app/public/hospitals/$filename" ]; then
                cp "$file" "storage/app/public/hospitals/" 2>/dev/null && restored_count=$((restored_count + 1)) || true
            fi
        fi
    done
    if [ $restored_count -gt 0 ]; then
        echo "✅ Restored $restored_count file(s) from $LATEST_BACKUP"
    else
        echo "ℹ️  All files already exist, no restore needed"
    fi
else
    echo "ℹ️  No backup found to restore"
fi

# Ensure .gitkeep exists
touch storage/app/public/hospitals/.gitkeep

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force 2>/dev/null || php artisan storage:link

# Set permissions
chmod -R 775 storage/app/public/hospitals 2>/dev/null || true

echo "✅ Deployment hooks completed!"


