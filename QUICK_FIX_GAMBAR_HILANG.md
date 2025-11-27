# Quick Fix: Gambar Hilang Saat Deploy

## 🚨 Masalah

Gambar logo tenant dan gambar artikel references hilang setiap kali ada deployment baru.

## ✅ Solusi Cepat (5 Menit)

### Opsi 1: Setup Persistent Storage (TERBAIK - 5 menit)

1. Login ke **Laravel Cloud Dashboard**
2. Pilih **Environment** → **Settings** → **Storage**
3. Klik **"Add Storage"** untuk setiap folder:

   **Storage 1: Hospitals**

   - Path: `/storage/app/public/hospitals`
   - Size: `1 GB`
   - Save

   **Storage 2: References**

   - Path: `/storage/app/public/references`
   - Size: `2 GB`
   - Save

4. **Selesai!** Gambar tidak akan hilang lagi.

### Opsi 2: Setup Deployment Hooks (Alternatif)

Jika Persistent Storage tidak tersedia:

1. **Before Deploy Hook:**

```bash
BACKUP_BASE="/home/forge/storage_backup"
BACKUP_DIR="$BACKUP_BASE/uploads_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_BASE"
[ -d "storage/app/public/hospitals" ] && mkdir -p "$BACKUP_DIR/hospitals" && cp -r storage/app/public/hospitals/* "$BACKUP_DIR/hospitals/" 2>/dev/null || true
[ -d "storage/app/public/references" ] && mkdir -p "$BACKUP_DIR/references" && cp -r storage/app/public/references/* "$BACKUP_DIR/references/" 2>/dev/null || true
```

2. **After Deploy Hook:**

```bash
BACKUP_BASE="/home/forge/storage_backup"
LATEST_BACKUP=$(ls -dt "$BACKUP_BASE"/uploads_* 2>/dev/null | head -1)
mkdir -p storage/app/public/hospitals storage/app/public/references
[ -d "$LATEST_BACKUP/hospitals" ] && cp -r "$LATEST_BACKUP/hospitals"/* storage/app/public/hospitals/ 2>/dev/null || true
[ -d "$LATEST_BACKUP/references" ] && cp -r "$LATEST_BACKUP/references"/* storage/app/public/references/ 2>/dev/null || true
php artisan storage:link --force
chmod -R 775 storage/app/public/hospitals storage/app/public/references
```

## 📋 Checklist

- [ ] Persistent Storage sudah di-setup (atau)
- [ ] Before Deploy Hook sudah di-setup
- [ ] After Deploy Hook sudah di-setup
- [ ] Deploy aplikasi
- [ ] Verifikasi gambar tidak hilang

## 📖 Dokumentasi Lengkap

Lihat `SOLUSI_GAMBAR_HILANG_SAAT_DEPLOY.md` untuk penjelasan detail.
