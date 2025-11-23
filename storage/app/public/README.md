# Storage Public Directory

Folder ini digunakan untuk menyimpan file yang di-upload oleh user, seperti logo hospital.

## Penting!

**File di folder ini TIDAK di-commit ke Git** karena:
- File upload adalah data user yang tidak boleh di-version control
- Ukuran file bisa besar
- File bisa berubah-ubah

## Setelah Pull/Deployment

Setelah melakukan `git pull` atau deployment, pastikan:

1. **Folder structure tetap ada** - File `.gitkeep` memastikan folder `hospitals/` tetap ada
2. **Symlink sudah dibuat** - Jalankan `php artisan storage:link` jika symlink belum ada
3. **Permission folder benar** - Pastikan folder bisa ditulis oleh web server:
   ```bash
   chmod -R 775 storage/app/public
   chown -R www-data:www-data storage/app/public  # atau user web server Anda
   ```

## File Logo

File logo hospital disimpan di `storage/app/public/hospitals/` dan diakses melalui symlink `public/storage/hospitals/`.

File logo **TIDAK akan hilang** jika:
- Folder `storage/app/public/hospitals/` tetap ada (dijamin oleh `.gitkeep`)
- Symlink `public/storage` sudah dibuat
- Permission folder benar

File logo **AKAN hilang** jika:
- Folder `storage/app/public` dihapus secara manual
- Server di-reset tanpa backup
- Deployment script menghapus folder storage

## Backup

**PENTING**: Selalu backup folder `storage/app/public` sebelum deployment jika ada file penting!

