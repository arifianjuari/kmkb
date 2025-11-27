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
BACKUP_DIR="$BACKUP_BASE/uploads_$(date +%Y%m%d_%H%M%S)"
BACKUP_NEEDED=false

# Create backup base directory (ensure it exists)
mkdir -p "$BACKUP_BASE" 2>/dev/null || true

# Backup hospitals
if [ -d "storage/app/public/hospitals" ] && [ "$(ls -A storage/app/public/hospitals 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up hospital logos..."
    mkdir -p "$BACKUP_DIR/hospitals"
    find storage/app/public/hospitals -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/hospitals/" \; 2>/dev/null || true
    if [ "$(ls -A "$BACKUP_DIR/hospitals" 2>/dev/null)" ]; then
        echo "✅ Hospitals backup created: $(ls -1 "$BACKUP_DIR/hospitals" | wc -l) file(s)"
        BACKUP_NEEDED=true
    fi
fi

# Backup references
if [ -d "storage/app/public/references" ] && [ "$(ls -A storage/app/public/references 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up reference images..."
    mkdir -p "$BACKUP_DIR/references"
    find storage/app/public/references -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/references/" \; 2>/dev/null || true
    if [ "$(ls -A "$BACKUP_DIR/references" 2>/dev/null)" ]; then
        echo "✅ References backup created: $(ls -1 "$BACKUP_DIR/references" | wc -l) file(s)"
        BACKUP_NEEDED=true
    fi
fi

if [ "$BACKUP_NEEDED" = true ]; then
    echo "✅ Backup created: $BACKUP_DIR"
    # Keep only last 5 backups
    ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | tail -n +6 | xargs rm -rf 2>/dev/null || true
else
    echo "ℹ️  No existing files to backup"
    rmdir "$BACKUP_DIR" 2>/dev/null || true
fi

# ============================================
# AFTER DEPLOY HOOK  
# ============================================
# Copy script ini ke "After Deploy" hook di Laravel Cloud

# Restore backed up files
echo "📥 Restoring uploaded files..."
mkdir -p storage/app/public/hospitals
mkdir -p storage/app/public/references

# Find latest backup
BACKUP_BASE="/home/forge/storage_backup"
LATEST_BACKUP=$(ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | head -1)
restored_total=0

if [ -n "$LATEST_BACKUP" ] && [ -d "$LATEST_BACKUP" ]; then
    echo "📦 Found backup: $LATEST_BACKUP"
    
    # Restore hospitals
    if [ -d "$LATEST_BACKUP/hospitals" ] && [ "$(ls -A "$LATEST_BACKUP/hospitals" 2>/dev/null)" ]; then
        restored_count=0
        for file in "$LATEST_BACKUP/hospitals"/*; do
            if [ -f "$file" ]; then
                filename=$(basename "$file")
                cp "$file" "storage/app/public/hospitals/" 2>/dev/null && restored_count=$((restored_count + 1)) || true
            fi
        done
        if [ $restored_count -gt 0 ]; then
            echo "✅ Restored $restored_count hospital file(s)"
            restored_total=$((restored_total + restored_count))
        else
            echo "ℹ️  No hospital files to restore"
        fi
    fi
    
    # Restore references
    if [ -d "$LATEST_BACKUP/references" ] && [ "$(ls -A "$LATEST_BACKUP/references" 2>/dev/null)" ]; then
        restored_count=0
        for file in "$LATEST_BACKUP/references"/*; do
            if [ -f "$file" ]; then
                filename=$(basename "$file")
                cp "$file" "storage/app/public/references/" 2>/dev/null && restored_count=$((restored_count + 1)) || true
            fi
        done
        if [ $restored_count -gt 0 ]; then
            echo "✅ Restored $restored_count reference file(s)"
            restored_total=$((restored_total + restored_count))
        else
            echo "ℹ️  No reference files to restore"
        fi
    fi
    
    if [ $restored_total -eq 0 ]; then
        echo "ℹ️  All files already exist, no restore needed"
    fi
else
    echo "⚠️  No backup found to restore (this is normal for first deploy)"
fi

# Ensure .gitkeep exists
touch storage/app/public/hospitals/.gitkeep
touch storage/app/public/references/.gitkeep

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link --force 2>/dev/null || php artisan storage:link

# Set permissions
chmod -R 775 storage/app/public/hospitals 2>/dev/null || true
chmod -R 775 storage/app/public/references 2>/dev/null || true

echo "✅ Deployment hooks completed!"


