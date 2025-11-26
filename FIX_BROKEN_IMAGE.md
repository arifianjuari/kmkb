# Fix Broken Image - Object Storage

## Masalah

Gambar broken image setelah setup Object Storage dengan disk name "public" di Laravel Cloud.

## Penyebab

1. **File belum di-upload ke Object Storage** - File masih di local storage yang terhapus saat deploy
2. **Disk name tidak cocok** - Disk name di Laravel Cloud adalah "public", tapi kode menggunakan disk lain
3. **URL tidak benar** - URL yang dihasilkan tidak sesuai dengan Object Storage endpoint

## Solusi

### 1. Pastikan Disk Name Cocok

Dari gambar yang Anda berikan:
- **Disk name di Laravel Cloud**: `public`
- **Bucket name**: `kmkb`
- **Default**: `Yes`

Kode sudah diperbaiki untuk menggunakan disk `public` yang akan otomatis di-override oleh Laravel Cloud menjadi S3.

### 2. Migrasi File ke Object Storage

File yang sudah ada di local storage perlu dimigrasikan ke Object Storage. Jalankan migrasi:

**Via Web Interface:**
1. Login sebagai admin
2. Akses: `https://kmkb.online/migrate-storage`
3. Klik "Mulai Migrasi"

**Via Artisan Command (jika tersedia):**
```bash
php artisan storage:migrate-to-s3
```

### 3. Verifikasi URL

Setelah migrasi, cek URL yang dihasilkan:

```php
use Illuminate\Support\Facades\Storage;

$hospital = \App\Models\Hospital::first();
$path = $hospital->logo_path;
echo Storage::disk('public')->url($path);
```

**Untuk Object Storage (S3):**
- URL akan seperti: `https://[bucket-endpoint]/hospitals/logo.png`
- Atau: `https://[bucket-id].r2.cloudflarestorage.com/hospitals/logo.png`

**Untuk Local Storage:**
- URL akan seperti: `https://kmkb.online/storage/hospitals/logo.png`

### 4. Clear Config Cache

Setelah setup Object Storage, clear config cache:

```bash
php artisan config:clear
php artisan config:cache
```

## Checklist

- [ ] Disk name di Laravel Cloud adalah "public" (sesuai gambar)
- [ ] Bucket sudah di-attach ke environment
- [ ] Credentials AWS sudah di-set di environment variables
- [ ] Config cache sudah di-clear dan di-rebuild
- [ ] File sudah dimigrasi ke Object Storage (via `/migrate-storage`)
- [ ] URL yang dihasilkan benar (dari bucket endpoint, bukan APP_URL/storage)

## Troubleshooting

### Gambar masih broken setelah migrasi

1. **Cek apakah file ada di Object Storage:**
   ```php
   Storage::disk('public')->exists('hospitals/logo.png')
   ```

2. **Cek URL yang dihasilkan:**
   ```php
   Storage::disk('public')->url('hospitals/logo.png')
   ```

3. **Cek CORS policy** - Pastikan bucket visibility adalah "Public" dan CORS sudah dikonfigurasi

4. **Cek browser console** - Lihat error di browser console untuk detail lebih lanjut

### File tidak bisa di-upload

1. Pastikan credentials AWS benar
2. Pastikan bucket sudah di-attach ke environment
3. Pastikan permissions bucket adalah "Read and write"

