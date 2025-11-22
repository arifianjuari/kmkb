# Panduan Deploy Aplikasi KMKB ke Laravel Cloud

Panduan lengkap langkah demi langkah untuk mendeploy aplikasi KMKB ke Laravel Cloud.

## Daftar Isi

1. [Persiapan Awal](#persiapan-awal)
2. [Setup Repository Git](#setup-repository-git)
3. [Persiapan Aplikasi](#persiapan-aplikasi)
4. [Setup Laravel Cloud](#setup-laravel-cloud)
5. [Konfigurasi Environment Variables](#konfigurasi-environment-variables)
6. [Setup Database](#setup-database)
7. [Build Assets Frontend](#build-assets-frontend)
8. [Deploy Aplikasi](#deploy-aplikasi)
9. [Verifikasi Deploy](#verifikasi-deploy)
10. [Troubleshooting](#troubleshooting)

---

## Persiapan Awal

### 1.1 Persyaratan

Pastikan Anda memiliki:
- ✅ Akun Laravel Cloud (daftar di [laravel.com/cloud](https://laravel.com/cloud))
- ✅ Akses ke repository Git (GitHub, GitLab, atau Bitbucket)
- ✅ Kredensial database MySQL
- ✅ Kredensial database SIMRS (jika diperlukan)
- ✅ File `.env` lokal yang sudah dikonfigurasi dengan benar

### 1.2 Persiapan File

Pastikan file-file berikut sudah siap:
- ✅ `composer.json` dan `composer.lock`
- ✅ `package.json` dan `package-lock.json`
- ✅ Semua file migration di folder `database/migrations/`
- ✅ File seeder jika diperlukan

---

## Setup Repository Git

### 2.1 Inisialisasi Git (jika belum ada)

```bash
cd /path/to/kmkb
git init
git add .
git commit -m "Initial commit"
```

### 2.2 Push ke Repository Remote

**Jika menggunakan GitHub:**

```bash
# Buat repository baru di GitHub terlebih dahulu
git remote add origin https://github.com/username/kmkb.git
git branch -M main
git push -u origin main
```

**Jika menggunakan GitLab:**

```bash
# Buat repository baru di GitLab terlebih dahulu
git remote add origin https://gitlab.com/username/kmkb.git
git branch -M main
git push -u origin main
```

**Jika menggunakan Bitbucket:**

```bash
# Buat repository baru di Bitbucket terlebih dahulu
git remote add origin https://bitbucket.org/username/kmkb.git
git branch -M main
git push -u origin main
```

### 2.3 Verifikasi Repository

Pastikan semua file penting sudah ter-commit:
- ✅ `composer.json` dan `composer.lock`
- ✅ `package.json` dan `package-lock.json`
- ✅ Folder `app/`, `config/`, `database/`, `routes/`, `resources/`
- ✅ File `artisan`, `vite.config.js`, `tailwind.config.js`
- ✅ Folder `public/` (kecuali `public/storage` dan `public/build`)

**Catatan:** Pastikan file `.env` TIDAK ter-commit ke repository (sudah ada di `.gitignore`).

---

## Persiapan Aplikasi

### 3.1 Buat File `.env.example`

Pastikan file `.env.example` ada dan berisi semua environment variables yang diperlukan:

```env
APP_NAME="KMKB Application"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-app.laravelcloud.com

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kmkb_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"

# SIMRS Database Connection
SIMRS_DB_HOST=127.0.0.1
SIMRS_DB_PORT=3306
SIMRS_DB_DATABASE=simrs
SIMRS_DB_USERNAME=simrs_user
SIMRS_DB_PASSWORD=simrs_password
```

### 3.2 Pastikan Build Scripts Sudah Benar

Periksa `package.json` sudah memiliki script build:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build"
  }
}
```

### 3.3 Commit Perubahan

```bash
git add .
git commit -m "Prepare for Laravel Cloud deployment"
git push
```

---

## Setup Laravel Cloud

### 4.1 Login ke Laravel Cloud

1. Buka browser dan kunjungi [laravel.com/cloud](https://laravel.com/cloud)
2. Login dengan akun Laravel Cloud Anda
3. Jika belum punya akun, daftar terlebih dahulu

### 4.2 Buat Project Baru

1. Di dashboard Laravel Cloud, klik tombol **"Create Project"** atau **"New Project"**
2. Isi informasi project:
   - **Project Name:** KMKB Application (atau nama yang Anda inginkan)
   - **Description:** (opsional) Deskripsi aplikasi
3. Klik **"Create Project"**

### 4.3 Connect Repository

1. Di halaman project, klik **"Connect Repository"** atau **"Link Repository"**
2. Pilih provider Git yang Anda gunakan:
   - **GitHub** - Klik "Connect GitHub" dan authorize
   - **GitLab** - Klik "Connect GitLab" dan authorize
   - **Bitbucket** - Klik "Connect Bitbucket" dan authorize
3. Pilih repository `kmkb` dari daftar
4. Klik **"Connect"** atau **"Link Repository"**

### 4.4 Buat Environment

1. Setelah repository terhubung, klik **"Create Environment"** atau **"New Environment"**
2. Pilih branch yang akan di-deploy (biasanya `main` atau `master`)
3. Isi informasi environment:
   - **Environment Name:** production (atau staging)
   - **PHP Version:** 8.1 atau 8.2 (sesuai requirement)
4. Klik **"Create Environment"**

---

## Konfigurasi Environment Variables

### 5.1 Akses Environment Variables

1. Di halaman environment, klik tab **"Environment"** atau **"Variables"**
2. Klik tombol **"Add Variable"** atau **"Edit Variables"**

### 5.2 Tambahkan Environment Variables

Tambahkan semua environment variables berikut satu per satu:

#### A. Application Variables

```
APP_NAME=KMKB Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.laravelcloud.com
```

**Catatan:** Ganti `your-app.laravelcloud.com` dengan URL yang diberikan Laravel Cloud.

#### B. Application Key

```
APP_KEY=
```

**Penting:** Biarkan kosong dulu, akan di-generate otomatis saat deploy pertama kali.

#### C. Database Variables

```
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

**Catatan:** Nilai-nilai ini akan diisi setelah setup database (lihat langkah 6).

#### D. SIMRS Database Variables (jika diperlukan)

```
SIMRS_DB_HOST=
SIMRS_DB_PORT=3306
SIMRS_DB_DATABASE=
SIMRS_DB_USERNAME=
SIMRS_DB_PASSWORD=
```

#### E. Session & Cache

```
SESSION_DRIVER=database
CACHE_DRIVER=database
QUEUE_CONNECTION=database
```

#### F. Mail Configuration (sesuai kebutuhan)

```
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### G. Logging

```
LOG_CHANNEL=stack
LOG_LEVEL=error
```

### 5.3 Simpan Environment Variables

Setelah semua variables ditambahkan, klik **"Save"** atau **"Update Environment"**.

---

## Setup Database

### 6.1 Buat Database di Laravel Cloud

1. Di halaman environment, klik tab **"Database"** atau **"Databases"**
2. Klik tombol **"Create Database"** atau **"Add Database"**
3. Pilih tipe database: **MySQL**
4. Pilih plan database sesuai kebutuhan:
   - **Development:** Untuk testing
   - **Production:** Untuk production
5. Isi informasi database:
   - **Database Name:** kmkb_db (atau nama yang Anda inginkan)
6. Klik **"Create Database"**

### 6.2 Dapatkan Database Credentials

Setelah database dibuat, Laravel Cloud akan menampilkan:
- **Database Host**
- **Database Port** (biasanya 3306)
- **Database Name**
- **Database Username**
- **Database Password**

**Penting:** Simpan credentials ini dengan aman!

### 6.3 Update Environment Variables Database

1. Kembali ke tab **"Environment"** atau **"Variables"**
2. Update environment variables database dengan credentials yang baru:

```
DB_HOST=<database_host_dari_laravel_cloud>
DB_PORT=3306
DB_DATABASE=<database_name>
DB_USERNAME=<database_username>
DB_PASSWORD=<database_password>
```

3. Klik **"Save"**

### 6.4 Import Database Schema

Ada dua cara untuk mengimport database:

#### Opsi A: Menggunakan Laravel Migrations (Recommended)

1. Di halaman environment, klik tab **"Deployments"**
2. Klik tombol **"Deploy Now"** untuk trigger deploy pertama
3. Setelah deploy selesai, buka **"SSH"** atau **"Terminal"** di environment
4. Jalankan command:

```bash
php artisan migrate --force
```

5. Jika ada seeder, jalankan:

```bash
php artisan db:seed --force
```

#### Opsi B: Import SQL File Langsung

1. Di halaman database, klik **"Database Manager"** atau **"phpMyAdmin"**
2. Login dengan database credentials
3. Pilih database yang sudah dibuat
4. Klik tab **"Import"**
5. Upload file SQL backup Anda (`kmkb_backup_20250902_150310.sql`)
6. Klik **"Go"** untuk import

**Catatan:** Jika menggunakan SQL backup, pastikan struktur database sesuai dengan migration terbaru.

---

## Build Assets Frontend

### 7.1 Setup Build Command di Laravel Cloud

1. Di halaman environment, klik tab **"Settings"** atau **"Build Settings"**
2. Cari bagian **"Build Command"** atau **"Build Scripts"**
3. Pastikan build command sudah terkonfigurasi:

```bash
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Jika belum ada, tambahkan build command tersebut
5. Klik **"Save"**

### 7.2 Verifikasi Node.js Version

1. Di build settings, pastikan Node.js version sudah sesuai
2. Untuk Vite 6.0, diperlukan Node.js 18+ atau 20+
3. Jika perlu, update Node.js version di settings

---

## Deploy Aplikasi

### 8.1 Trigger Deploy Pertama

1. Di halaman environment, klik tab **"Deployments"**
2. Klik tombol **"Deploy Now"** atau **"Deploy"**
3. Laravel Cloud akan:
   - Clone repository
   - Install Composer dependencies
   - Install NPM dependencies
   - Build frontend assets (Vite)
   - Run build commands
   - Deploy aplikasi

### 8.2 Monitor Deploy Process

1. Anda bisa melihat progress deploy di halaman **"Deployments"**
2. Klik pada deployment untuk melihat log detail
3. Tunggu hingga status berubah menjadi **"Deployed"** atau **"Success"**

### 8.3 Generate Application Key (jika belum ada)

Jika `APP_KEY` masih kosong, setelah deploy pertama:

1. Buka **"SSH"** atau **"Terminal"** di environment
2. Jalankan command:

```bash
php artisan key:generate --force
```

3. Atau update environment variable `APP_KEY` secara manual di Laravel Cloud dashboard

### 8.4 Run Migrations

1. Buka **"SSH"** atau **"Terminal"** di environment
2. Jalankan command:

```bash
php artisan migrate --force
```

3. Jika ada seeder, jalankan:

```bash
php artisan db:seed --force
```

### 8.5 Setup Storage Link

1. Di terminal SSH, jalankan:

```bash
php artisan storage:link
```

### 8.6 Clear dan Cache Config

1. Di terminal SSH, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Verifikasi Deploy

### 9.1 Akses Aplikasi

1. Di halaman environment, cari **"Application URL"** atau **"Domain"**
2. Klik URL tersebut atau copy ke browser
3. Aplikasi seharusnya sudah bisa diakses

### 9.2 Test Fitur Utama

1. **Test Login:**
   - Buka halaman login
   - Login dengan user yang sudah ada di database
   - Pastikan login berhasil

2. **Test Database Connection:**
   - Pastikan data bisa di-load dari database
   - Test CRUD operations

3. **Test SIMRS Connection (jika ada):**
   - Test koneksi ke database SIMRS
   - Pastikan data bisa di-sync

4. **Test File Upload:**
   - Test upload file (jika ada fitur upload)
   - Pastikan storage link sudah benar

### 9.3 Check Logs

1. Di halaman environment, klik tab **"Logs"**
2. Periksa log untuk error atau warning
3. Jika ada error, lihat bagian [Troubleshooting](#troubleshooting)

---

## Troubleshooting

### 10.1 Error: APP_KEY is not set

**Solusi:**
1. Buka SSH terminal
2. Jalankan: `php artisan key:generate --force`
3. Atau set `APP_KEY` di environment variables

### 10.2 Error: Database Connection Failed

**Solusi:**
1. Periksa database credentials di environment variables
2. Pastikan database sudah dibuat di Laravel Cloud
3. Pastikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sudah benar
4. Test koneksi dari SSH terminal:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

### 10.3 Error: Assets Not Loading (404)

**Solusi:**
1. Pastikan build command sudah dijalankan
2. Pastikan `npm run build` berhasil
3. Periksa folder `public/build` sudah ada
4. Clear cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### 10.4 Error: Storage Link Not Found

**Solusi:**
1. Jalankan di SSH terminal:
   ```bash
   php artisan storage:link
   ```
2. Pastikan folder `storage/app/public` ada

### 10.5 Error: Migration Failed

**Solusi:**
1. Periksa log error di Laravel Cloud
2. Pastikan database sudah dibuat
3. Pastikan user database punya permission untuk create table
4. Jalankan migration dengan verbose:
   ```bash
   php artisan migrate --force -vvv
   ```

### 10.6 Error: SIMRS Connection Failed

**Solusi:**
1. Periksa `SIMRS_DB_*` environment variables
2. Pastikan database SIMRS bisa diakses dari server Laravel Cloud
3. Jika SIMRS database di network internal, pastikan Laravel Cloud bisa mengaksesnya
4. Test koneksi dari SSH terminal:
   ```bash
   php artisan tinker
   DB::connection('simrs')->getPdo();
   ```

### 10.7 Error: Build Failed (NPM/Node)

**Solusi:**
1. Pastikan Node.js version sesuai (18+ atau 20+)
2. Periksa `package.json` dan `package-lock.json` sudah ter-commit
3. Pastikan build command sudah benar
4. Periksa log build untuk detail error

### 10.8 Error: Permission Denied

**Solusi:**
1. Pastikan folder `storage` dan `bootstrap/cache` writable
2. Di SSH terminal, jalankan:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

### 10.9 Error: Memory Limit Exceeded

**Solusi:**
1. Update `memory_limit` di PHP settings (jika memungkinkan)
2. Atau optimize build process
3. Contact Laravel Cloud support untuk increase memory limit

### 10.10 Error: Timeout During Deploy

**Solusi:**
1. Periksa ukuran repository (jangan commit `node_modules` atau `vendor`)
2. Pastikan `.gitignore` sudah benar
3. Optimize build process
4. Contact Laravel Cloud support

---

## Tips dan Best Practices

### 11.1 Environment Variables

- ✅ Jangan commit file `.env` ke repository
- ✅ Gunakan `.env.example` sebagai template
- ✅ Simpan credentials dengan aman
- ✅ Gunakan environment variables untuk semua konfigurasi sensitif

### 11.2 Database

- ✅ Backup database secara berkala
- ✅ Gunakan migration untuk perubahan schema
- ✅ Test migration di environment staging terlebih dahulu

### 11.3 Deploy

- ✅ Test di environment staging sebelum production
- ✅ Monitor logs setelah deploy
- ✅ Setup auto-deploy dari branch tertentu (opsional)
- ✅ Gunakan deployment hooks untuk run migration otomatis

### 11.4 Security

- ✅ Set `APP_DEBUG=false` di production
- ✅ Gunakan HTTPS (Laravel Cloud sudah menyediakan)
- ✅ Update dependencies secara berkala
- ✅ Gunakan strong passwords untuk database

### 11.5 Performance

- ✅ Enable caching (config, route, view)
- ✅ Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- ✅ Minify assets di production
- ✅ Monitor performance metrics di Laravel Cloud dashboard

---

## Setup Auto-Deploy (Opsional)

### 12.1 Enable Auto-Deploy

1. Di halaman environment, klik tab **"Settings"**
2. Cari bagian **"Auto-Deploy"** atau **"Deployment Hooks"**
3. Enable auto-deploy untuk branch tertentu (misalnya `main`)
4. Set deployment hooks jika diperlukan:
   - **Before Deploy:** (opsional)
   - **After Deploy:** 
     ```bash
     php artisan migrate --force
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     ```

### 12.2 Test Auto-Deploy

1. Buat perubahan kecil di code
2. Commit dan push ke branch yang di-set untuk auto-deploy
3. Deploy seharusnya trigger otomatis
4. Monitor deployment di dashboard

---

## Setup Custom Domain (Opsional)

### 13.1 Add Custom Domain

1. Di halaman environment, klik tab **"Domains"**
2. Klik **"Add Domain"**
3. Masukkan domain Anda (misalnya: `kmkb.yourdomain.com`)
4. Ikuti instruksi untuk setup DNS:
   - Tambahkan CNAME record di DNS provider
   - Point ke domain yang diberikan Laravel Cloud
5. Tunggu hingga DNS propagate (bisa beberapa menit hingga 24 jam)
6. Laravel Cloud akan otomatis setup SSL certificate

### 13.2 Update APP_URL

1. Update environment variable `APP_URL` dengan custom domain
2. Redeploy aplikasi

---

## Monitoring dan Maintenance

### 14.1 Monitor Application

1. Gunakan dashboard Laravel Cloud untuk monitor:
   - Application logs
   - Database performance
   - Server resources
   - Error tracking

### 14.2 Regular Maintenance

1. **Update Dependencies:**
   ```bash
   composer update
   npm update
   ```

2. **Clear Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

3. **Optimize:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   composer install --optimize-autoloader --no-dev
   ```

---

## Support dan Resources

### 15.1 Laravel Cloud Documentation

- [Laravel Cloud Docs](https://laravel.com/docs/cloud)
- [Laravel Cloud Support](https://laravel.com/support)

### 15.2 Laravel Documentation

- [Laravel 10 Docs](https://laravel.com/docs/10.x)
- [Laravel Deployment](https://laravel.com/docs/10.x/deployment)

### 15.3 Community

- [Laravel Forums](https://laracasts.com/discuss)
- [Laravel Discord](https://discord.gg/laravel)

---

## Checklist Deploy

Gunakan checklist ini untuk memastikan semua langkah sudah dilakukan:

### Pre-Deploy
- [ ] Repository Git sudah dibuat dan di-push
- [ ] File `.env.example` sudah lengkap
- [ ] Build scripts sudah benar
- [ ] Semua dependencies sudah ter-commit

### Laravel Cloud Setup
- [ ] Akun Laravel Cloud sudah dibuat
- [ ] Project sudah dibuat
- [ ] Repository sudah di-connect
- [ ] Environment sudah dibuat

### Configuration
- [ ] Environment variables sudah di-set
- [ ] Database sudah dibuat
- [ ] Database credentials sudah di-update
- [ ] APP_KEY sudah di-generate

### Deploy
- [ ] Deploy pertama sudah berhasil
- [ ] Migrations sudah di-run
- [ ] Seeders sudah di-run (jika ada)
- [ ] Storage link sudah dibuat
- [ ] Cache sudah di-clear dan di-rebuild

### Verification
- [ ] Aplikasi bisa diakses
- [ ] Login berfungsi
- [ ] Database connection berhasil
- [ ] Assets (CSS/JS) loading dengan benar
- [ ] File upload berfungsi (jika ada)
- [ ] SIMRS connection berhasil (jika diperlukan)

### Post-Deploy
- [ ] Logs sudah di-check
- [ ] Error tracking sudah di-setup
- [ ] Monitoring sudah di-setup
- [ ] Backup strategy sudah direncanakan

---

**Selamat! Aplikasi KMKB Anda sudah berhasil di-deploy ke Laravel Cloud! 🎉**

Jika ada pertanyaan atau masalah, silakan refer ke bagian [Troubleshooting](#troubleshooting) atau hubungi Laravel Cloud support.

