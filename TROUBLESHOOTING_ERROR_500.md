# Troubleshooting Error 500 - Object Storage

## Masalah

Error 500 setelah menambahkan environment variables untuk Object Storage.

## Penyebab

1. **Package `league/flysystem-aws-s3-v3` belum terinstall**
2. **Config filesystems error** - disk 'uploads' didefinisikan sebagai S3 tapi package tidak terinstall
3. **Credentials tidak valid** atau format salah

## Solusi

### 1. Pastikan Package Terinstall

Tambahkan ke `composer.json`:
```json
"require": {
    "league/flysystem-aws-s3-v3": "^3.0"
}
```

Kemudian jalankan:
```bash
composer update league/flysystem-aws-s3-v3
```

### 2. Periksa Environment Variables

Pastikan format environment variables benar (tanpa quotes untuk nilai):

```env
# ✅ BENAR
APP_URL=https://kmkb.online
AWS_BUCKET=fls-a06d3989-5a04-4df7-9878-6dc64e127315
AWS_ACCESS_KEY_ID=90646fc59d3470369ab442dabd1827bb

# ❌ SALAH (jangan pakai quotes)
APP_URL="https://kmkb.online"
AWS_BUCKET="fls-a06d3989-5a04-4df7-9878-6dc64e127315"
```

### 3. Clear Config Cache

Setelah menambahkan environment variables, clear config cache:

```bash
php artisan config:clear
php artisan config:cache
```

### 4. Verifikasi Package Terinstall

Cek apakah package sudah terinstall:
```bash
composer show league/flysystem-aws-s3-v3
```

Jika tidak terinstall, install dengan:
```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

### 5. Test Object Storage Connection

Jalankan di tinker atau buat test route:
```php
use Illuminate\Support\Facades\Storage;

try {
    $disk = Storage::disk('uploads');
    // Test connection
    $disk->exists('test');
    echo "✅ Object Storage connected";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

## Checklist

- [ ] Package `league/flysystem-aws-s3-v3` sudah ditambahkan ke `composer.json`
- [ ] Package sudah terinstall (`composer show` menampilkan package)
- [ ] Environment variables sudah di-set tanpa quotes
- [ ] Config cache sudah di-clear dan di-rebuild
- [ ] Credentials AWS sudah benar dan valid
- [ ] Bucket sudah di-attach ke environment di Laravel Cloud

## Fallback

Jika masih error, aplikasi akan otomatis fallback ke local storage (`public` disk) karena helper function `uploads_disk()` akan return 'public' jika credentials tidak tersedia.

