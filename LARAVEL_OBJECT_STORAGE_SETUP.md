# Setup Laravel Object Storage untuk Uploads

Dokumen ini menjelaskan cara mengonfigurasi aplikasi untuk menggunakan Laravel Object Storage (S3-compatible bucket) untuk menyimpan gambar hospitals dan references.

## Prerequisites

1. Pastikan package `league/flysystem-aws-s3-v3` sudah terinstall:
```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

2. Buat bucket di Laravel Cloud dashboard:
   - Buka Laravel Cloud dashboard
   - Pilih environment Anda
   - Klik "Add bucket" pada infrastructure canvas
   - Pilih "Laravel Object Storage" sebagai bucket type
   - Beri nama bucket (misal: `hospital`)
   - Pilih visibility (Public untuk gambar yang perlu diakses publik)
   - Set disk name (misal: `uploads`)

## Konfigurasi Environment Variables

Setelah bucket dibuat, Laravel Cloud akan memberikan credentials. Tambahkan ke file `.env` di production:

```env
AWS_BUCKET=fls-a06d3989-5a04-4df7-9878-6dc64e127315
AWS_DEFAULT_REGION=auto
AWS_ENDPOINT=https://367be3a2035528943240074d0096e0cd.r2.cloudflarestorage.com
AWS_ACCESS_KEY_ID=90646fc59d3470369ab442dabd1827bb
AWS_SECRET_ACCESS_KEY=18838e27f6fec23b657e0dc01b8103ea69190b3ac2a0ba2ba63bcc504da15653
AWS_USE_PATH_STYLE_ENDPOINT=false
```

**Catatan:** Untuk production di Laravel Cloud, credentials ini biasanya sudah otomatis di-set oleh Laravel Cloud. Pastikan bucket sudah di-attach ke environment Anda.

## Cara Kerja

Aplikasi akan otomatis menggunakan Object Storage jika credentials AWS tersedia, atau fallback ke local storage (`public` disk) jika tidak ada.

### Disk Configuration

File `config/filesystems.php` sudah dikonfigurasi dengan disk `uploads` yang:
- Menggunakan S3 driver jika `AWS_ACCESS_KEY_ID` tersedia
- Fallback ke local driver jika tidak ada credentials

### Helper Functions

Dua helper function tersedia:

1. **`uploads_disk()`**: Mengembalikan nama disk yang digunakan ('uploads' atau 'public')
2. **`storage_url($path)`**: Mengembalikan URL lengkap untuk file di storage

### Penggunaan di Controller

```php
use Illuminate\Support\Facades\Storage;

// Upload file
$uploadDisk = uploads_disk();
$file->storeAs('hospitals', $filename, $uploadDisk);

// Check exists
if (Storage::disk($uploadDisk)->exists($path)) {
    // File exists
}

// Delete file
Storage::disk($uploadDisk)->delete($path);
```

### Penggunaan di Blade Template

```blade
{{-- Menggunakan helper function --}}
<img src="{{ storage_url($hospital->logo_path) }}" alt="Logo">

{{-- Atau manual --}}
@if(Storage::disk(uploads_disk())->exists($path))
    <img src="{{ Storage::disk(uploads_disk())->url($path) }}" alt="Image">
@endif
```

## Migrasi dari Local ke Object Storage

Jika Anda sudah punya gambar di local storage dan ingin migrasi ke Object Storage:

1. Pastikan credentials AWS sudah di-set di `.env`
2. Jalankan script migrasi (opsional):
```php
// Di tinker atau buat command artisan
$files = Storage::disk('public')->allFiles('hospitals');
foreach ($files as $file) {
    $content = Storage::disk('public')->get($file);
    Storage::disk('uploads')->put($file, $content);
}
```

## Testing

1. **Local (tanpa credentials)**: Aplikasi akan menggunakan local storage
2. **Production (dengan credentials)**: Aplikasi akan menggunakan Object Storage

Untuk test di local dengan Object Storage, tambahkan credentials ke `.env` lokal Anda.

## Troubleshooting

### Gambar tidak tampil

1. Pastikan bucket visibility adalah "Public" jika gambar perlu diakses publik
2. Pastikan CORS policy sudah dikonfigurasi dengan benar di Laravel Cloud
3. Check URL yang dihasilkan: `Storage::disk('uploads')->url($path)`
4. Untuk S3, pastikan `AWS_URL` atau endpoint sudah benar

### Error: "Access Denied"

1. Pastikan credentials AWS benar
2. Pastikan bucket sudah di-attach ke environment
3. Pastikan access key permission adalah "Read and write"

### Error: "Bucket not found"

1. Pastikan `AWS_BUCKET` benar
2. Pastikan bucket sudah di-attach ke environment di Laravel Cloud

## Referensi

- [Laravel Cloud Object Storage Documentation](https://cloud.laravel.com/docs/resources/object-storage)
- [Laravel Filesystem Documentation](https://laravel.com/docs/filesystem)

