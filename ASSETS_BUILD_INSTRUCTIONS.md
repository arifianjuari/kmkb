# Instruksi Build Assets untuk KMKB

## Masalah yang Terjadi
Jika layout berantakan, tombol tidak terformat, card tidak ada style dan padding, kemungkinan besar **assets CSS/JS belum di-build**.

## Solusi

### 1. Install Dependencies (Jika Belum)
```bash
npm install
```

### 2. Build Assets untuk Production
```bash
npm run build
```

Ini akan:
- Mengcompile Tailwind CSS
- Mengcompile JavaScript
- Menghasilkan file di `public/build/`

### 3. Untuk Development (Hot Reload)
Jika sedang development dan ingin auto-reload saat ada perubahan:
```bash
npm run dev
```

**PENTING:** Jangan tutup terminal ini saat development. Biarkan berjalan di background.

### 4. Verifikasi Build Berhasil
Setelah `npm run build`, pastikan file berikut ada:
- `public/build/manifest.json`
- `public/build/assets/app-*.css` (sekitar 82KB)
- `public/build/assets/app-*.js` (sekitar 80KB)

### 5. Clear Cache Laravel (Jika Masih Ada Masalah)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Checklist

- [ ] `node_modules/` folder ada dan dependencies terinstall
- [ ] `npm run build` sudah dijalankan
- [ ] File di `public/build/` sudah ter-generate
- [ ] `@vite(['resources/css/app.css', 'resources/js/app.js'])` ada di blade template
- [ ] Cache Laravel sudah di-clear

## Troubleshooting

### Assets tidak ter-load
1. Pastikan `APP_ENV` di `.env` sesuai (production/development)
2. Pastikan `VITE_APP_URL` di `.env` sesuai dengan URL aplikasi
3. Clear browser cache (Ctrl+Shift+R atau Cmd+Shift+R)

### Style masih tidak muncul
1. Pastikan Tailwind config sudah include semua file blade:
   ```js
   content: [
       './resources/views/**/*.blade.php',
   ]
   ```
2. Rebuild assets: `npm run build`
3. Hard refresh browser

### Error saat build
1. Hapus `node_modules` dan `package-lock.json`
2. Jalankan `npm install` lagi
3. Jalankan `npm run build`

## Catatan Penting

- **Setiap kali mengubah CSS atau Tailwind config**, jalankan `npm run build` lagi
- **Untuk development**, gunakan `npm run dev` (jangan tutup terminal)
- **Untuk production**, selalu gunakan `npm run build` sebelum deploy

