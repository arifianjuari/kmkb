# Setup Cepat: Upload Gambar References di Laravel Cloud

## 🚀 Langkah-Langkah Setup

### 1. Setup After Deploy Hook (WAJIB)

1. Login ke **Laravel Cloud Dashboard**
2. Pilih **environment** (Production/Staging)
3. Klik **"Settings"** → **"Deployment Hooks"**
4. Scroll ke bagian **"After Deploy"**
5. Paste script berikut:

```bash
# Create storage directories
mkdir -p storage/app/public/references
touch storage/app/public/references/.gitkeep

# Create storage link
php artisan storage:link --force 2>/dev/null || php artisan storage:link

# Set permissions
chmod -R 775 storage/app/public/references 2>/dev/null || true
```

6. Klik **"Save"**

### 2. Setup Before Deploy Hook (OPSIONAL - untuk backup)

Jika ingin gambar tidak hilang saat deploy, tambahkan di **"Before Deploy"**:

```bash
# Backup reference images
BACKUP_BASE="/home/forge/storage_backup"
BACKUP_DIR="$BACKUP_BASE/uploads_$(date +%Y%m%d_%H%M%S)"

if [ -d "storage/app/public/references" ] && [ "$(ls -A storage/app/public/references 2>/dev/null | grep -v '^\.gitkeep$')" ]; then
    echo "💾 Backing up reference images..."
    mkdir -p "$BACKUP_DIR/references"
    find storage/app/public/references -type f ! -name '.gitkeep' -exec cp {} "$BACKUP_DIR/references/" \; 2>/dev/null
    if [ "$(ls -A "$BACKUP_DIR/references" 2>/dev/null)" ]; then
        echo "✅ References backup created"
    fi
fi
```

Dan update **After Deploy Hook** untuk restore (lihat file `laravel-cloud-hooks.sh` untuk script lengkap).

### 3. Deploy & Verifikasi

1. **Deploy aplikasi** (push ke repository atau trigger manual)
2. **Cek log deployment** untuk memastikan tidak ada error
3. **Test upload gambar:**
   - Login ke aplikasi
   - Buka **References** → **Tambah Referensi**
   - Upload gambar JPEG/PNG
   - Simpan dan cek apakah gambar tampil

### 4. Manual Setup (Jika Hook Gagal)

Jika setelah deploy gambar masih tidak tampil, jalankan via SSH:

```bash
# 1. Buat folder
mkdir -p storage/app/public/references
touch storage/app/public/references/.gitkeep

# 2. Set permission
chmod -R 775 storage/app/public/references

# 3. Buat storage link
php artisan storage:link

# 4. Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## ✅ Checklist

Setelah setup, pastikan:

- [ ] After Deploy Hook sudah di-setup
- [ ] Deploy berhasil tanpa error
- [ ] Folder `storage/app/public/references` ada di server
- [ ] Storage link `public/storage` ada (symlink)
- [ ] Permission folder 775
- [ ] Test upload gambar berhasil
- [ ] Gambar tampil di halaman detail

## 🔍 Verifikasi

### Cek Storage Link
```bash
ls -la public/storage
# Harus menampilkan: lrwxrwxrwx ... public/storage -> ../storage/app/public
```

### Cek Folder References
```bash
ls -la storage/app/public/references
# Harus menampilkan folder dengan .gitkeep
```

### Test URL Gambar
Akses di browser:
```
https://your-app.laravelcloud.com/storage/references/nama-file.jpg
```

Jika gambar tampil, berarti setup sudah benar! ✅

## 📚 Dokumentasi Lengkap

Untuk dokumentasi lengkap, lihat file:
- `SETUP_REFERENCES_IMAGES.md` - Panduan detail
- `laravel-cloud-hooks.sh` - Script backup/restore lengkap
- `IMAGE_STORAGE_SETUP.md` - Setup storage umum

## 🆘 Troubleshooting

**Gambar tidak tampil?**
1. Cek storage link: `ls -la public/storage`
2. Cek permission: `ls -la storage/app/public/references`
3. Buat storage link manual: `php artisan storage:link`
4. Clear cache: `php artisan config:clear && php artisan cache:clear`

**Upload gagal?**
1. Cek permission: `chmod -R 775 storage/app/public/references`
2. Cek disk space: `df -h`
3. Cek PHP upload limit di Laravel Cloud settings

**Gambar hilang setelah deploy?**
1. Pastikan Before/After Deploy Hook sudah di-setup
2. Cek log deployment untuk error
3. Pastikan folder `storage/app/public/references` tidak di-ignore di `.gitignore`

---

**Selamat! Setup selesai! 🎉**

