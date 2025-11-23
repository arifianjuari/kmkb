# Instruksi Clear Cache untuk Melihat Perubahan

## Masalah yang sudah diperbaiki:

- Overlay gelap yang menutupi sidebar di desktop sudah dihapus
- File `resources/views/layouts/app.blade.php` sudah diupdate dengan benar
- Build Vite sudah dijalankan
- Cache Laravel sudah dibersihkan

## Langkah untuk melihat perubahan:

### 1. Hard Refresh Browser

Tekan salah satu kombinasi ini di browser:

- **Chrome/Edge (Mac)**: `Cmd + Shift + R`
- **Chrome/Edge (Windows)**: `Ctrl + Shift + R`
- **Safari**: `Cmd + Option + R`
- **Firefox**: `Ctrl + Shift + R` atau `Cmd + Shift + R`

### 2. Clear Browser Cache Completely

Jika hard refresh tidak berhasil:

- Chrome: `Cmd/Ctrl + Shift + Delete` → Pilih "Cached images and files" → Clear
- Atau buka DevTools (F12) → klik kanan tombol refresh → pilih "Empty Cache and Hard Reload"

### 3. Cek di Incognito/Private Mode

Buka halaman di mode incognito untuk memastikan tidak ada cache:

- Chrome: `Cmd/Ctrl + Shift + N`
- Safari: `Cmd + Shift + N`

### 4. Verifikasi Perubahan Berhasil

Setelah refresh, sidebar di desktop seharusnya:

- ✅ Terlihat solid dengan background slate-800 (abu gelap)
- ✅ TIDAK ada overlay hitam yang menutupi
- ✅ Menu items terlihat jelas, tidak transparan
- ✅ Overlay hitam hanya muncul di mobile saat hamburger menu diklik

## Jika masih bermasalah:

Jalankan command ini di terminal:

```bash
php artisan view:clear
php artisan cache:clear
npm run build
```

Lalu hard refresh browser lagi.

## Technical Details (untuk developer):

Perubahan yang dilakukan:

- Mengubah dari `:class="sidebarOpen ? 'lg:hidden' : 'hidden'"`
- Menjadi `x-show="sidebarOpen"` + static `class="lg:hidden"`
- Ini memastikan CSS `lg:hidden` selalu aktif di desktop, tidak bisa di-override oleh Alpine.js
