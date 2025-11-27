# Solusi: Gambar Logo dan Artikel Hilang Saat Deploy

## 🔴 Masalah

Gambar logo tenant dan gambar artikel references selalu hilang setiap kali ada deployment baru di Laravel Cloud.

## 🔍 Root Cause

Laravel Cloud melakukan **fresh clone** setiap deployment, yang menghapus semua file yang tidak di-track oleh Git, termasuk:
- `storage/app/public/hospitals/` (logo tenant)
- `storage/app/public/references/` (gambar artikel)

Script backup/restore di dalam project directory (`storage/app/backup_*`) juga ikut terhapus karena folder `storage/app` ikut ter-reset.

## ✅ Solusi: Setup Persistent Storage (RECOMMENDED)

**Ini adalah solusi TERBAIK dan PERMANEN** - gambar tidak akan hilang lagi setelah setup ini.

### Langkah-langkah Setup:

1. **Login ke Laravel Cloud Dashboard**
   - Buka https://cloud.laravel.com
   - Pilih environment (Production/Staging)

2. **Buka Settings → Storage**

3. **Tambahkan Persistent Storage untuk Hospitals:**
   - Klik **"Add Storage"**
   - **Path**: `/storage/app/public/hospitals`
   - **Size**: `1` GB (atau sesuai kebutuhan)
   - **Description**: `Hospital logos storage`
   - Klik **"Save"**

4. **Tambahkan Persistent Storage untuk References:**
   - Klik **"Add Storage"** lagi
   - **Path**: `/storage/app/public/references`
   - **Size**: `2` GB (atau sesuai kebutuhan, gambar artikel bisa lebih banyak)
   - **Description**: `Reference article images storage`
   - Klik **"Save"**

5. **Verifikasi:**
   - Setelah deploy berikutnya, folder-folder ini akan **persisten**
   - File tidak akan hilang lagi

### Keuntungan Persistent Storage:

✅ **Permanen** - File tidak akan hilang saat deploy  
✅ **Otomatis** - Tidak perlu setup hooks atau script  
✅ **Reliable** - Dikelola langsung oleh Laravel Cloud  
✅ **Scalable** - Bisa di-resize sesuai kebutuhan  

---

## 🔧 Solusi Alternatif: Deployment Hooks

Jika Persistent Storage tidak tersedia atau belum bisa digunakan, gunakan deployment hooks dengan backup ke lokasi di luar project directory.

### Setup Before Deploy Hook:

1. **Buka Laravel Cloud Dashboard**
2. **Pilih Environment → Settings → Deployment Hooks**
3. **Di bagian "Before Deploy", paste script berikut:**

```bash
#!/bin/bash
# Backup existing uploaded files to persistent location (outside project directory)
BACKUP_BASE="/home/forge/storage_backup"
BACKUP_DIR="$BACKUP_BASE/uploads_$(date +%Y%m%d_%H%M%S)"
BACKUP_NEEDED=false

# Create backup base directory
mkdir -p "$BACKUP_BASE"

# Backup hospitals
if [ -d "storage/app/public/hospitals" ] && [ "$(ls -A storage/app/public/hospitals 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up hospital logos..."
    mkdir -p "$BACKUP_DIR/hospitals"
    find storage/app/public/hospitals -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/hospitals/" \; 2>/dev/null
    if [ "$(ls -A "$BACKUP_DIR/hospitals" 2>/dev/null)" ]; then
        echo "✅ Hospitals backup created"
        BACKUP_NEEDED=true
    fi
fi

# Backup references
if [ -d "storage/app/public/references" ] && [ "$(ls -A storage/app/public/references 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up reference images..."
    mkdir -p "$BACKUP_DIR/references"
    find storage/app/public/references -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/references/" \; 2>/dev/null
    if [ "$(ls -A "$BACKUP_DIR/references" 2>/dev/null)" ]; then
        echo "✅ References backup created"
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
```

### Setup After Deploy Hook:

**Di bagian "After Deploy", paste script berikut:**

```bash
#!/bin/bash
# Restore backed up files
echo "📥 Restoring uploaded files..."
mkdir -p storage/app/public/hospitals
mkdir -p storage/app/public/references

# Find latest backup
BACKUP_BASE="/home/forge/storage_backup"
LATEST_BACKUP=$(ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | head -1)
restored_total=0

if [ -n "$LATEST_BACKUP" ] && [ -d "$LATEST_BACKUP" ]; then
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
        fi
    fi
    
    if [ $restored_total -eq 0 ]; then
        echo "ℹ️  All files already exist, no restore needed"
    fi
else
    echo "ℹ️  No backup found to restore"
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
```

---

## 📋 Checklist Setup

### Jika Menggunakan Persistent Storage:

- [ ] Login ke Laravel Cloud Dashboard
- [ ] Buka Settings → Storage
- [ ] Tambahkan Persistent Storage untuk `/storage/app/public/hospitals`
- [ ] Tambahkan Persistent Storage untuk `/storage/app/public/references`
- [ ] Deploy aplikasi
- [ ] Verifikasi gambar tidak hilang setelah deploy

### Jika Menggunakan Deployment Hooks:

- [ ] Login ke Laravel Cloud Dashboard
- [ ] Buka Settings → Deployment Hooks
- [ ] Paste script Before Deploy
- [ ] Paste script After Deploy
- [ ] Deploy aplikasi
- [ ] Cek log deployment untuk memastikan backup/restore berjalan
- [ ] Verifikasi gambar tidak hilang setelah deploy

---

## 🔍 Troubleshooting

### Gambar Masih Hilang Setelah Setup Persistent Storage?

1. **Cek apakah Persistent Storage sudah di-mount:**
   ```bash
   # Via SSH
   df -h | grep hospitals
   df -h | grep references
   ```

2. **Pastikan path benar:**
   - Harus: `/storage/app/public/hospitals`
   - Harus: `/storage/app/public/references`
   - Bukan: `storage/app/public/hospitals` (tanpa leading slash)

3. **Cek permission:**
   ```bash
   ls -la storage/app/public/hospitals
   ls -la storage/app/public/references
   chmod -R 775 storage/app/public/hospitals
   chmod -R 775 storage/app/public/references
   ```

### Gambar Masih Hilang Setelah Setup Hooks?

1. **Cek log deployment:**
   - Di Laravel Cloud Dashboard, buka deployment log
   - Cari pesan "Backing up..." dan "Restoring..."
   - Pastikan tidak ada error

2. **Cek apakah backup directory ada:**
   ```bash
   # Via SSH
   ls -la /home/forge/storage_backup/
   ```

3. **Test backup/restore manual:**
   ```bash
   # Via SSH
   mkdir -p /home/forge/storage_backup/test
   cp storage/app/public/hospitals/* /home/forge/storage_backup/test/ 2>/dev/null || echo "No files to backup"
   ls -la /home/forge/storage_backup/test/
   ```

4. **Pastikan script dijalankan:**
   - Cek apakah script di "Before Deploy" dan "After Deploy" sudah benar
   - Pastikan tidak ada typo atau syntax error

---

## 🎯 Rekomendasi

**Gunakan Persistent Storage (Opsi 1)** karena:
- ✅ Paling reliable dan permanen
- ✅ Tidak perlu maintenance
- ✅ Tidak ada risiko kehilangan data
- ✅ Dikelola langsung oleh Laravel Cloud

Deployment Hooks (Opsi 2) hanya digunakan jika:
- Persistent Storage tidak tersedia di plan Anda
- Atau sebagai backup tambahan

---

## 📝 Catatan Penting

1. **Setelah setup Persistent Storage, gambar yang sudah ada sebelumnya mungkin tidak otomatis muncul.** Anda perlu:
   - Upload ulang logo tenant
   - Upload ulang gambar artikel yang hilang

2. **Untuk gambar baru setelah setup, tidak akan hilang lagi.**

3. **Backup manual (opsional):**
   - Sebelum deploy pertama setelah setup, backup gambar yang ada
   - Simpan di lokasi aman
   - Restore manual jika diperlukan

---

**Setelah setup ini, gambar tidak akan hilang lagi saat deploy! 🎉**

