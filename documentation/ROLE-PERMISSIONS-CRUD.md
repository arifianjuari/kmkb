# Dokumentasi Sistem Role dan Permissions CRUD

Dokumentasi ini menjelaskan pengaturan kebijakan (policy) dan hak akses CRUD untuk masing-masing role di webapp KMKB.

---

## 1. Daftar Role

### 1.1 Role yang Terdefinisi di Kode

Berdasarkan `app/Models/User.php`, role yang tersedia adalah:

- **superadmin** - Super Administrator (akses penuh ke semua hospital)
- **admin** - Administrator (akses penuh di hospital yang ditugaskan)
- **mutu** - Tim Mutu/Kualitas
- **klaim** - Tim Klaim
- **manajemen** - Manajemen
- **observer** - Observer (read-only access ke semua aktivitas)

### 1.2 Role di Dokumentasi (Target Implementation)

Berdasarkan `documentation/MENU-STRUCTURE-DESIGN.md`, role yang direncanakan:

- **Superadmin** - System Administrator
- **Admin** - Hospital Administrator
- **Financial Manager** - Manajer Keuangan
- **Costing Analyst** - Analis Costing
- **Medical Committee** - Komite Medis
- **Pathway Designer** - Desainer Clinical Pathway
- **Case Manager** - Manajer Kasus
- **Auditor** - Auditor

**Catatan:** Ada ketidaksesuaian antara role di kode dan dokumentasi. Role di kode saat ini lebih sederhana, sedangkan dokumentasi merencanakan role yang lebih detail.

---

## 2. Sistem Authorization

Webapp ini menggunakan beberapa mekanisme authorization:

### 2.1 Laravel Policy

**Lokasi:** `app/Policies/`

**Policy yang Tersedia:**

- `ReferencePolicy` - Untuk model Reference
- `BasePolicy` - Base class untuk policy lain

**Cara Kerja:**

- Policy menggunakan `authorizeResource()` di controller
- Policy mengecek role user dan hospital_id
- Superadmin memiliki akses penuh
- User lain harus dalam hospital yang sama

**Contoh Implementasi:**

```php
// ReferencePolicy.php
public function canManage(User $user): bool
{
    if ($user->isSuperadmin()) {
        return true;
    }
    return in_array($user->role, [
        User::ROLE_ADMIN,
        User::ROLE_MUTU,
        User::ROLE_KLAIM,
        User::ROLE_MANAJEMEN,
    ], true);
}
```

### 2.2 Role Middleware

**Lokasi:** `app/Http/Middleware/RoleMiddleware.php`

**Cara Kerja:**

- Middleware `role:admin` atau `role:superadmin` di routes
- Mengecek apakah user memiliki role yang sesuai
- Superadmin memiliki akses ke semua role

**Contoh Penggunaan:**

```php
// routes/web.php
Route::resource('hospitals', HospitalController::class)
    ->middleware(['auth', 'verified', 'role:superadmin']);

Route::middleware('role:admin')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('jkn-cbg-codes', JknCbgCodeController::class);
});
```

### 2.3 Manual Authorization Checks

**Lokasi:** Di dalam controller methods

**Cara Kerja:**

- Manual check menggunakan `hasRole()`, `isSuperadmin()`
- Check di view menggunakan `@can`, `@if(auth()->user()->hasRole())`
- Custom authorization logic di controller

**Contoh:**

```php
// UserController.php
if (!auth()->user()->isSuperadmin() && $request->role === User::ROLE_SUPERADMIN) {
    return redirect()->back()
        ->with('error', 'You are not authorized to assign superadmin role.');
}
```

### 2.4 Hospital Context

**Lokasi:** `app/Http/Middleware/SetHospital.php`

**Cara Kerja:**

- Superadmin harus memilih hospital context
- User biasa otomatis menggunakan hospital_id mereka
- Data di-filter berdasarkan hospital_id di session

---

## 3. Hak Akses CRUD per Role

### 3.1 Observer

**Akses:** Read-only ke semua aktivitas di hospital yang ditugaskan

| Modul              | Create | Read | Update | Delete | Keterangan |
| ------------------ | ------ | ---- | ------ | ------ | ---------- |
| Dashboard          | ❌     | ✅   | ❌     | ❌     | -          |
| Cost Centers       | ❌     | ✅   | ❌     | ❌     | -          |
| Expense Categories | ❌     | ✅   | ❌     | ❌     | -          |
| Allocation Drivers | ❌     | ✅   | ❌     | ❌     | -          |
| Tariff Classes     | ❌     | ✅   | ❌     | ❌     | -          |
| Cost References    | ❌     | ✅   | ❌     | ❌     | -          |
| JKN CBG Codes      | ❌     | ✅   | ❌     | ❌     | -          |
| GL Expenses        | ❌     | ✅   | ❌     | ❌     | -          |
| Driver Statistics  | ❌     | ✅   | ❌     | ❌     | -          |
| Service Volumes    | ❌     | ✅   | ❌     | ❌     | -          |
| Allocation Maps    | ❌     | ✅   | ❌     | ❌     | -          |
| Allocation Results | ❌     | ✅   | ❌     | ❌     | -          |
| Tariff Simulation  | ❌     | ✅   | ❌     | ❌     | -          |
| Final Tariffs      | ❌     | ✅   | ❌     | ❌     | -          |
| Clinical Pathways  | ❌     | ✅   | ❌     | ❌     | -          |
| Patient Cases      | ❌     | ✅   | ❌     | ❌     | -          |
| References         | ❌     | ✅   | ❌     | ❌     | -          |
| Reports            | ❌     | ✅   | ❌     | ❌     | -          |
| Audit Logs         | ❌     | ✅   | ❌     | ❌     | -          |
| SIMRS Integration  | ❌     | ✅   | ❌     | ❌     | -          |

**Route Protection:**

- Tidak ada middleware khusus, menggunakan policy checks
- Semua route read (index, show) dapat diakses
- Semua route write (create, update, delete) diblokir oleh policy

**Keterangan:**

- Observer dapat melihat semua data di hospital mereka
- Observer tidak dapat membuat, mengubah, atau menghapus data apapun
- Observer cocok untuk auditor, reviewer, atau pihak yang hanya perlu monitoring
- Observer dapat mengakses semua laporan dan dashboard untuk monitoring

---

### 3.2 Superadmin

**Akses:** Penuh ke semua hospital dan semua fitur

| Modul            | Create | Read | Update | Delete | Keterangan       |
| ---------------- | ------ | ---- | ------ | ------ | ---------------- |
| Hospitals        | ✅     | ✅   | ✅     | ✅     | Hanya superadmin |
| Users            | ✅     | ✅   | ✅     | ✅     | Semua hospital   |
| Audit Logs       | ❌     | ✅   | ❌     | ✅     | Semua hospital   |
| System Settings  | ✅     | ✅   | ✅     | ❌     | -                |
| Semua Modul Lain | ✅     | ✅   | ✅     | ✅     | Semua hospital   |

**Route Protection:**

- `/hospitals/*` - `role:superadmin` middleware

**Keterangan:**

- Superadmin dapat mengakses semua data dari semua hospital
- Superadmin dapat membuat user dengan role apapun termasuk superadmin
- Superadmin dapat mengubah hospital_id user

---

### 3.3 Admin

**Akses:** Penuh di hospital yang ditugaskan

| Modul              | Create | Read | Update | Delete | Keterangan                                         |
| ------------------ | ------ | ---- | ------ | ------ | -------------------------------------------------- |
| Users              | ✅     | ✅   | ✅     | ✅     | Hanya hospital sendiri, tidak bisa buat superadmin |
| Cost Centers       | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Expense Categories | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Allocation Drivers | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Tariff Classes     | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Cost References    | ✅     | ✅   | ✅     | ✅     | -                                                  |
| JKN CBG Codes      | ✅     | ✅   | ✅     | ✅     | -                                                  |
| GL Expenses        | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Driver Statistics  | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Service Volumes    | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Allocation Maps    | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Allocation Results | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Tariff Simulation  | ✅     | ✅   | ✅     | ❌     | -                                                  |
| Final Tariffs      | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Clinical Pathways  | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Patient Cases      | ✅     | ✅   | ✅     | ✅     | -                                                  |
| References         | ✅     | ✅   | ✅     | ✅     | -                                                  |
| Audit Logs         | ❌     | ✅   | ❌     | ✅     | -                                                  |
| SIMRS Integration  | ✅     | ✅   | ✅     | ❌     | -                                                  |

**Route Protection:**

- `/users/*` - `role:admin` middleware
- `/jkn-cbg-codes/*` - `role:admin` middleware (CRUD)
- `/audit-logs/*` - `role:admin` middleware

**Keterangan:**

- Admin tidak dapat membuat user dengan role superadmin
- Admin tidak dapat mengubah hospital_id user
- Admin hanya dapat mengelola data di hospital mereka sendiri

---

### 3.4 Mutu

**Akses:** Terbatas untuk modul mutu/kualitas

| Modul             | Create | Read | Update | Delete | Keterangan |
| ----------------- | ------ | ---- | ------ | ------ | ---------- |
| Cost References   | ✅     | ✅   | ✅     | ✅     | -          |
| References        | ✅     | ✅   | ✅     | ✅     | -          |
| Clinical Pathways | ✅     | ✅   | ✅     | ✅     | -          |
| Patient Cases     | ✅     | ✅   | ✅     | ✅     | -          |
| Reports           | ❌     | ✅   | ❌     | ❌     | -          |

**Route Protection:**

- Tidak ada middleware khusus, menggunakan policy checks

**Keterangan:**

- Role mutu dapat mengelola Clinical Pathways dan Patient Cases
- Dapat mengelola Cost References dan References
- Akses read-only untuk reports

---

### 3.5 Klaim

**Akses:** Terbatas untuk modul klaim

| Modul           | Create | Read | Update | Delete | Keterangan |
| --------------- | ------ | ---- | ------ | ------ | ---------- |
| Cost References | ✅     | ✅   | ✅     | ✅     | -          |
| References      | ✅     | ✅   | ✅     | ✅     | -          |
| Patient Cases   | ✅     | ✅   | ✅     | ✅     | -          |
| Reports         | ❌     | ✅   | ❌     | ❌     | -          |

**Keterangan:**

- Role klaim fokus pada pengelolaan kasus pasien
- Dapat mengelola Cost References dan References
- Akses read-only untuk reports

---

### 3.6 Manajemen

**Akses:** Terbatas untuk modul manajemen

| Modul           | Create | Read | Update | Delete | Keterangan |
| --------------- | ------ | ---- | ------ | ------ | ---------- |
| Cost References | ✅     | ✅   | ✅     | ✅     | -          |
| References      | ✅     | ✅   | ✅     | ✅     | -          |
| Reports         | ❌     | ✅   | ❌     | ❌     | -          |
| Dashboard       | ❌     | ✅   | ❌     | ❌     | -          |

**Keterangan:**

- Role manajemen memiliki akses read untuk dashboard dan reports
- Dapat mengelola Cost References dan References
- Fokus pada monitoring dan analisis

---

## 4. Detail Hak Akses per Modul

### 4.1 Dashboard

**Controller:** `DashboardController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ❌     | ✅   | ❌     | ❌     |
| Admin      | ❌     | ✅   | ❌     | ❌     |
| Mutu       | ❌     | ✅   | ❌     | ❌     |
| Klaim      | ❌     | ✅   | ❌     | ❌     |
| Manajemen  | ❌     | ✅   | ❌     | ❌     |
| Observer   | ❌     | ✅   | ❌     | ❌     |

**Authorization:** Semua user terautentikasi dapat mengakses dashboard

---

### 4.2 Hospitals

**Controller:** `HospitalController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ❌     | ❌   | ❌     | ❌     |
| Mutu       | ❌     | ❌   | ❌     | ❌     |
| Klaim      | ❌     | ❌   | ❌     | ❌     |
| Manajemen  | ❌     | ❌   | ❌     | ❌     |

**Route:** `/hospitals/*` dengan middleware `role:superadmin`

---

### 4.3 Users

**Controller:** `UserController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ❌     | ❌   | ❌     | ❌     |
| Klaim      | ❌     | ❌   | ❌     | ❌     |
| Manajemen  | ❌     | ❌   | ❌     | ❌     |

**Route:** `/users/*` dengan middleware `role:admin`

**Restrictions:**

- Admin tidak dapat membuat user dengan role superadmin
- Admin tidak dapat mengubah hospital_id user
- Admin hanya dapat mengelola user di hospital mereka sendiri

---

### 4.4 Cost Centers

**Controller:** `CostCenterController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ❌     | ❌   | ❌     | ❌     |
| Klaim      | ❌     | ❌   | ❌     | ❌     |
| Manajemen  | ❌     | ❌   | ❌     | ❌     |

**Authorization:** Manual check di controller (belum ada policy)

---

### 4.5 Expense Categories

**Controller:** `ExpenseCategoryController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ❌     | ❌   | ❌     | ❌     |
| Klaim      | ❌     | ❌   | ❌     | ❌     |
| Manajemen  | ❌     | ❌   | ❌     | ❌     |

**Authorization:** Manual check di controller (belum ada policy)

---

### 4.6 Cost References

**Controller:** `CostReferenceController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ✅     | ✅   | ✅     | ✅     |
| Klaim      | ✅     | ✅   | ✅     | ✅     |
| Manajemen  | ✅     | ✅   | ✅     | ✅     |

**Authorization:** Belum ada policy khusus, menggunakan hospital_id filtering

---

### 4.7 References (Knowledge References)

**Controller:** `ReferenceController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ✅     | ✅   | ✅     | ✅     |
| Klaim      | ✅     | ✅   | ✅     | ✅     |
| Manajemen  | ✅     | ✅   | ✅     | ✅     |
| Observer   | ❌     | ✅   | ❌     | ❌     |

**Authorization:** Menggunakan `ReferencePolicy`

**Policy Logic:**

- `viewAny`: Semua user yang terautentikasi
- `view`: Superadmin, Observer, atau user dengan hospital yang sama
- `create`: Admin, Mutu, Klaim, Manajemen (Observer diblokir)
- `update`: Admin, Mutu, Klaim, Manajemen + hospital yang sama (Observer diblokir)
- `delete`: Admin, Mutu, Klaim, Manajemen + hospital yang sama (Observer diblokir)

**Policy Logic:**

- `viewAny`: Semua user yang terautentikasi
- `view`: Superadmin atau user dengan hospital yang sama
- `create`: Admin, Mutu, Klaim, Manajemen
- `update`: Admin, Mutu, Klaim, Manajemen + hospital yang sama
- `delete`: Admin, Mutu, Klaim, Manajemen + hospital yang sama

---

### 4.8 Clinical Pathways

**Controller:** `PathwayController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ✅     | ✅   | ✅     | ✅     |
| Klaim      | ❌     | ✅   | ❌     | ❌     |
| Manajemen  | ❌     | ✅   | ❌     | ❌     |
| Observer   | ❌     | ✅   | ❌     | ❌     |

**Authorization:** Manual check di view dan controller

**Catatan:** Observer dapat melihat semua pathways di hospital mereka, tetapi tidak dapat membuat, mengubah, atau menghapus.

**View Checks:**

```blade
@if(auth()->user()?->hasRole('mutu') || auth()->user()?->hasRole('admin'))
    // Edit/Delete buttons
@endif
```

---

### 4.9 Patient Cases

**Controller:** `PatientCaseController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ✅     | ✅   | ✅     | ✅     |
| Klaim      | ✅     | ✅   | ✅     | ✅     |
| Manajemen  | ❌     | ✅   | ❌     | ❌     |
| Observer   | ❌     | ✅   | ❌     | ❌     |

**Authorization:** Manual check di model dan controller

**Catatan:** Observer dapat melihat semua patient cases di hospital mereka, tetapi tidak dapat membuat, mengubah, atau menghapus.

**Model Check:**

```php
// PatientCase.php
if (auth()->check() && auth()->user()->hasRole('superadmin')) {
    // Superadmin can see all cases
}
```

---

### 4.10 JKN CBG Codes

**Controller:** `JknCbgCodeController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ✅     | ✅   | ✅     | ✅     |
| Admin      | ✅     | ✅   | ✅     | ✅     |
| Mutu       | ❌     | ✅   | ❌     | ❌     |
| Klaim      | ❌     | ✅   | ❌     | ❌     |
| Manajemen  | ❌     | ✅   | ❌     | ❌     |
| Observer   | ❌     | ✅   | ❌     | ❌     |

**Route Protection:**

- CRUD: `role:admin` middleware
- Search/Tariff lookup: Semua user terautentikasi (termasuk Observer)

---

### 4.11 Audit Logs

**Controller:** `AuditLogController`

| Role       | Create | Read | Update | Delete |
| ---------- | ------ | ---- | ------ | ------ |
| Superadmin | ❌     | ✅   | ❌     | ✅     |
| Admin      | ❌     | ✅   | ❌     | ✅     |
| Observer   | ❌     | ✅   | ❌     | ❌     |
| Mutu       | ❌     | ❌   | ❌     | ❌     |
| Klaim      | ❌     | ❌   | ❌     | ❌     |
| Manajemen  | ❌     | ❌   | ❌     | ❌     |

**Route:** `/audit-logs/*` dengan middleware `role:admin`

**Catatan:** Observer dapat melihat audit logs untuk monitoring, tetapi tidak dapat menghapus logs.

---

## 5. Pola Authorization yang Digunakan

### 5.1 Hospital Isolation

Semua data di-filter berdasarkan `hospital_id`:

- User biasa: Otomatis menggunakan `hospital_id` mereka
- Superadmin: Harus memilih hospital context terlebih dahulu

**Implementasi:**

```php
// Di controller
$query = Model::where('hospital_id', hospital('id'));
```

### 5.2 Role-Based Access Control (RBAC)

**Tingkat 1: Route Level**

- Middleware `role:admin` atau `role:superadmin`
- Blokir akses ke route tertentu

**Tingkat 2: Controller Level**

- Manual check di controller methods
- `$this->authorizeResource()` untuk policy

**Tingkat 3: View Level**

- `@can()` directive
- `@if(auth()->user()->hasRole())` checks
- Hide/show UI elements berdasarkan role

### 5.3 Policy Pattern

**Base Policy:**

- `belongsToSameHospital()` - Check hospital isolation
- Dapat di-extend oleh policy lain

**Reference Policy:**

- `canManage()` - Check role untuk manage operations
- Superadmin selalu true
- Admin, Mutu, Klaim, Manajemen dapat manage

---

## 6. Rekomendasi untuk Pengembangan

### 6.1 Konsistensi Role

**Masalah:** Ada ketidaksesuaian antara role di kode dan dokumentasi.

**Rekomendasi:**

1. Tentukan mapping antara role kode dan role dokumentasi
2. Atau implementasikan role baru sesuai dokumentasi
3. Buat migration untuk update role existing

### 6.2 Policy Coverage

**Masalah:** Tidak semua modul memiliki policy.

**Rekomendasi:**

1. Buat policy untuk semua modul utama:
   - `CostCenterPolicy`
   - `ExpenseCategoryPolicy`
   - `PathwayPolicy`
   - `PatientCasePolicy`
   - dll
2. Register semua policy di `AuthServiceProvider`
3. Gunakan `authorizeResource()` di controller
4. **Penting:** Pastikan semua policy mengecek role Observer untuk read-only access
   - Method `view()` dan `viewAny()` harus allow Observer
   - Method `create()`, `update()`, `delete()` harus block Observer

### 6.3 Centralized Permission Management

**Rekomendasi:**

1. Buat helper class `PermissionChecker`
2. Centralize semua permission logic
3. Buat constants untuk permission names

### 6.4 Documentation

**Rekomendasi:**

1. Update dokumentasi ini ketika ada perubahan
2. Buat unit tests untuk authorization
3. Dokumentasikan edge cases

---

## 7. Testing Authorization

### 7.1 Test Cases yang Perlu

1. **Superadmin Access:**

   - Dapat akses semua hospital
   - Dapat membuat user superadmin
   - Dapat mengubah hospital_id user

2. **Admin Access:**

   - Tidak dapat membuat superadmin
   - Tidak dapat mengubah hospital_id
   - Hanya akses hospital sendiri

3. **Role Restrictions:**

   - Mutu/Klaim/Manajemen hanya akses modul tertentu
   - Hospital isolation bekerja dengan benar

4. **Policy Tests:**
   - Test semua policy methods
   - Test hospital isolation
   - Test role-based restrictions

---

## 8. Kesimpulan

Sistem authorization saat ini menggunakan kombinasi:

- **Laravel Policy** (untuk References)
- **Role Middleware** (untuk route protection)
- **Manual Checks** (di controller dan view)

**Kekuatan:**

- Hospital isolation bekerja dengan baik
- Superadmin memiliki kontrol penuh
- Role-based access sudah diimplementasikan
- **Role Observer** telah ditambahkan untuk read-only access

**Area untuk Improvement:**

- Konsistensi role naming
- Policy coverage untuk semua modul (perlu update semua policy untuk support Observer)
- Centralized permission management
- Update semua controller untuk block Observer dari write operations
- Dokumentasi yang lebih lengkap

**Catatan Penting:**

- Role Observer sudah ditambahkan di User model dan ReferencePolicy
- Perlu update controller lain untuk memastikan Observer tidak dapat melakukan create/update/delete
- Perlu update view untuk hide create/edit/delete buttons untuk Observer

---

**Dokumen ini terakhir diupdate:** {{ date('Y-m-d') }}

**Maintainer:** Development Team
