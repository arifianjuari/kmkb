# Panduan Deployment Role Observer

Dokumentasi ini menjelaskan langkah-langkah untuk deploy role 'observer' ke server produksi.

---

## 1. Ringkasan Perubahan

Role 'observer' telah ditambahkan dengan karakteristik:
- **Read-only access** ke semua modul di hospital yang ditugaskan
- Dapat melihat semua data untuk monitoring dan audit
- Tidak dapat membuat, mengubah, atau menghapus data apapun

---

## 2. File yang Telah Diupdate

### 2.1 Model & Policy
- ✅ `app/Models/User.php` - Menambahkan `ROLE_OBSERVER` constant dan method `isObserver()`
- ✅ `app/Policies/BasePolicy.php` - Menambahkan helper method `canView()` untuk read-only access
- ✅ `app/Policies/ReferencePolicy.php` - Update untuk support observer read-only

### 2.2 Controller
- ✅ `app/Http/Controllers/UserController.php` - Update validasi untuk include 'observer'

### 2.3 Routes
- ✅ `routes/web.php` - Update audit logs route untuk allow observer read access

### 2.4 Database
- ✅ `database/seeders/UsersTableSeeder.php` - Menambahkan user observer example
- ✅ `database/factories/UserFactory.php` - Menambahkan factory state untuk observer

### 2.5 Views
- ✅ `resources/views/users/index.blade.php` - Menambahkan filter observer
- ✅ `resources/views/users/create.blade.php` - Menambahkan option observer
- ✅ `resources/views/users/edit.blade.php` - Menambahkan option observer
- ✅ `resources/views/users/show.blade.php` - Menambahkan badge observer

### 2.6 Helper
- ✅ `app/Helpers/RoleHelper.php` - Helper class untuk role operations (baru)

### 2.7 Dokumentasi
- ✅ `documentation/ROLE-PERMISSIONS-CRUD.md` - Update dokumentasi lengkap

---

## 3. Langkah-Langkah Deployment

### 3.1 Pre-Deployment Checklist

- [ ] Backup database production
- [ ] Backup file aplikasi
- [ ] Test di environment staging terlebih dahulu
- [ ] Review semua perubahan kode
- [ ] Pastikan tidak ada breaking changes

### 3.2 Deployment Steps

#### Step 1: Pull Latest Code

```bash
cd /path/to/production
git pull origin main  # atau branch yang digunakan
```

#### Step 2: Install Dependencies (jika ada)

```bash
composer install --no-dev --optimize-autoloader
npm install --production  # jika menggunakan npm
npm run build  # jika menggunakan build tools
```

#### Step 3: Run Migrations (jika ada)

**Catatan:** Tidak ada migration baru yang diperlukan karena role disimpan sebagai string di kolom `role` yang sudah ada.

```bash
php artisan migrate
```

#### Step 4: Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### Step 5: Optimize (Opsional)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 6: Seed Observer User (Opsional)

Jika ingin membuat user observer example di production:

```bash
php artisan db:seed --class=UsersTableSeeder
```

**PENTING:** Hanya jalankan jika ingin membuat user example. User observer dapat dibuat melalui UI oleh admin.

---

## 4. Verifikasi Deployment

### 4.1 Checklist Verifikasi

- [ ] Login sebagai admin
- [ ] Buka halaman Users (`/users`)
- [ ] Verifikasi filter role menampilkan "Observer"
- [ ] Buat user baru dengan role "Observer"
- [ ] Login sebagai user observer
- [ ] Verifikasi dapat melihat semua modul (read-only)
- [ ] Verifikasi tidak dapat create/edit/delete data
- [ ] Verifikasi tombol create/edit/delete tidak muncul atau disabled
- [ ] Verifikasi dapat mengakses audit logs (read-only)

### 4.2 Test Cases

#### Test 1: Create Observer User
1. Login sebagai admin
2. Buka `/users/create`
3. Pilih role "Observer (Read-only)"
4. Isi form dan submit
5. Verifikasi user berhasil dibuat

#### Test 2: Observer Read Access
1. Login sebagai observer
2. Buka berbagai modul:
   - Dashboard ✅
   - Cost Centers ✅
   - Expense Categories ✅
   - Clinical Pathways ✅
   - Patient Cases ✅
   - Reports ✅
   - Audit Logs ✅
3. Verifikasi semua dapat diakses (read-only)

#### Test 3: Observer Write Restriction
1. Login sebagai observer
2. Coba akses route create/edit/delete:
   - `/cost-centers/create` → harus diblokir
   - `/expense-categories/create` → harus diblokir
   - `/pathways/create` → harus diblokir
3. Verifikasi semua write operations diblokir

---

## 5. Rollback Plan

Jika terjadi masalah, lakukan rollback:

### 5.1 Rollback Code

```bash
cd /path/to/production
git revert HEAD  # atau commit hash yang ingin di-rollback
```

### 5.2 Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 5.3 Restore Database (jika diperlukan)

```bash
# Restore dari backup yang dibuat sebelumnya
mysql -u username -p database_name < backup.sql
```

---

## 6. Post-Deployment

### 6.1 Monitoring

- Monitor error logs untuk beberapa hari pertama
- Monitor aktivitas user observer
- Verifikasi tidak ada issue dengan authorization

### 6.2 User Training

Jika diperlukan, berikan training kepada user observer tentang:
- Fitur yang dapat diakses (read-only)
- Cara menggunakan dashboard dan reports
- Cara mengakses audit logs

### 6.3 Documentation

Update dokumentasi internal jika diperlukan:
- User manual
- Admin guide
- API documentation (jika ada)

---

## 7. Troubleshooting

### 7.1 Observer Tidak Bisa Login

**Kemungkinan Penyebab:**
- User belum dibuat dengan benar
- Password tidak di-set

**Solusi:**
- Verifikasi user di database
- Reset password melalui admin

### 7.2 Observer Bisa Create/Edit/Delete

**Kemungkinan Penyebab:**
- Policy belum di-update untuk modul tertentu
- Controller belum di-update

**Solusi:**
- Update policy untuk modul yang bermasalah
- Update controller untuk block observer dari write operations
- Update view untuk hide create/edit/delete buttons

### 7.3 Observer Tidak Bisa Lihat Data

**Kemungkinan Penyebab:**
- Hospital isolation issue
- Policy belum di-update

**Solusi:**
- Verifikasi `hospital_id` user observer
- Update policy untuk allow observer view access

---

## 8. Catatan Penting

### 8.1 Controller yang Perlu Diupdate

Beberapa controller mungkin belum fully support observer. Controller yang perlu di-review:

- `CostCenterController` - Belum ada policy
- `ExpenseCategoryController` - Belum ada policy
- `PathwayController` - Manual checks, perlu update
- `PatientCaseController` - Manual checks, perlu update
- Dan controller lainnya

**Rekomendasi:** Buat policy untuk semua modul utama dan pastikan observer di-handle dengan benar.

### 8.2 View yang Perlu Diupdate

Beberapa view mungkin perlu di-update untuk hide create/edit/delete buttons untuk observer:

- Semua index views
- Semua show views
- Semua form views

**Rekomendasi:** Gunakan `@can()` directive atau `@if(auth()->user()->isObserver())` untuk hide buttons.

### 8.3 Testing

Pastikan untuk test secara menyeluruh sebelum deploy ke production:
- Unit tests untuk authorization
- Feature tests untuk observer access
- Integration tests untuk full workflow

---

## 9. Support

Jika ada pertanyaan atau issue, hubungi:
- Development Team
- System Administrator

---

**Dokumen ini terakhir diupdate:** {{ date('Y-m-d') }}

**Versi:** 1.0

