# Setup Kredensial Database SIMRS untuk Laravel Cloud

Panduan lengkap untuk mengatur kredensial database SIMRS di Laravel Cloud.

## 📋 Daftar Isi

1. [Persiapan Kredensial](#persiapan-kredensial)
2. [Konfigurasi di Laravel Cloud](#konfigurasi-di-laravel-cloud)
3. [Network Access Setup](#network-access-setup)
4. [Test Koneksi](#test-koneksi)
5. [Troubleshooting](#troubleshooting)

---

## Persiapan Kredensial

### 1.1 Informasi yang Diperlukan

Sebelum mengatur kredensial di Laravel Cloud, pastikan Anda sudah memiliki informasi berikut dari Administrator SIMRS:

- ✅ **Host/Server:** IP Address atau hostname server database SIMRS
  - Contoh: `192.168.1.100` atau `simrs-db.hospital.local`
  
- ✅ **Port:** Port database (biasanya 3306 untuk MySQL)
  - Default: `3306`
  
- ✅ **Database Name:** Nama database SIMRS
  - Contoh: `simrs_db`, `rs_simrs`, dll
  
- ✅ **Username:** Username untuk akses database
  - **PENTING:** Gunakan user dengan permission **READ-ONLY** untuk keamanan
  
- ✅ **Password:** Password untuk user database

### 1.2 Rekomendasi Keamanan

1. **Gunakan Read-Only User:**
   ```sql
   -- Contoh membuat user read-only di MySQL
   CREATE USER 'kmkb_readonly'@'%' IDENTIFIED BY 'strong_password_here';
   GRANT SELECT ON simrs_db.* TO 'kmkb_readonly'@'%';
   FLUSH PRIVILEGES;
   ```

2. **Jangan Gunakan Root User:**
   - Hindari menggunakan root user atau user dengan full access
   - Ini untuk keamanan dan best practice

3. **Gunakan Strong Password:**
   - Minimal 16 karakter
   - Kombinasi huruf besar, huruf kecil, angka, dan simbol

---

## Konfigurasi di Laravel Cloud

### 2.1 Akses Environment Variables

1. Login ke [Laravel Cloud Dashboard](https://laravel.com/cloud)
2. Pilih project Anda
3. Pilih environment (production/staging)
4. Klik tab **"Environment"** atau **"Variables"**
5. Klik tombol **"Add Variable"** atau **"Edit Variables"**

### 2.2 Tambahkan SIMRS Database Variables

Tambahkan environment variables berikut satu per satu:

#### Variable 1: SIMRS_DB_HOST
```
Key: SIMRS_DB_HOST
Value: <ip_address_atau_hostname_server_simrs>
```
**Contoh:**
```
SIMRS_DB_HOST=192.168.1.100
```
atau
```
SIMRS_DB_HOST=simrs-db.hospital.local
```

#### Variable 2: SIMRS_DB_PORT
```
Key: SIMRS_DB_PORT
Value: 3306
```
**Catatan:** Port default MySQL adalah 3306. Sesuaikan jika berbeda.

#### Variable 3: SIMRS_DB_DATABASE
```
Key: SIMRS_DB_DATABASE
Value: <nama_database_simrs>
```
**Contoh:**
```
SIMRS_DB_DATABASE=simrs_db
```

#### Variable 4: SIMRS_DB_USERNAME
```
Key: SIMRS_DB_USERNAME
Value: <username_database_simrs>
```
**Contoh:**
```
SIMRS_DB_USERNAME=kmkb_readonly
```

#### Variable 5: SIMRS_DB_PASSWORD
```
Key: SIMRS_DB_PASSWORD
Value: <password_database_simrs>
```
**Contoh:**
```
SIMRS_DB_PASSWORD=your_secure_password_here
```

### 2.3 Simpan Environment Variables

1. Setelah semua variables ditambahkan, klik **"Save"** atau **"Update Environment"**
2. Tunggu beberapa detik hingga variables tersimpan

### 2.4 Verifikasi Variables

Pastikan semua variables sudah terdaftar:
- ✅ `SIMRS_DB_HOST`
- ✅ `SIMRS_DB_PORT`
- ✅ `SIMRS_DB_DATABASE`
- ✅ `SIMRS_DB_USERNAME`
- ✅ `SIMRS_DB_PASSWORD`

---

## Network Access Setup

### 3.1 Skenario 1: Database SIMRS di Cloud (Public Access)

Jika database SIMRS sudah di-deploy di cloud (AWS RDS, Google Cloud SQL, dll):

1. **Whitelist IP Laravel Cloud:**
   - Dapatkan IP address server Laravel Cloud Anda
   - Tambahkan IP tersebut ke whitelist/firewall database SIMRS
   - Atau gunakan security group yang mengizinkan akses dari IP Laravel Cloud

2. **Enable Public Access:**
   - Pastikan database SIMRS memiliki public endpoint
   - Pastikan firewall mengizinkan koneksi dari internet

### 3.2 Skenario 2: Database SIMRS di On-Premise (Internal Network)

Jika database SIMRS berada di server internal/on-premise:

#### Opsi A: VPN Connection (Recommended)

1. **Setup VPN di Laravel Cloud:**
   - Laravel Cloud support VPN connection
   - Setup VPN tunnel ke network internal hospital
   - Pastikan database SIMRS bisa diakses melalui VPN

2. **Konfigurasi:**
   - Gunakan internal IP address database SIMRS
   - Pastikan routing VPN sudah benar

#### Opsi B: Public IP dengan Firewall

1. **Expose Database ke Internet:**
   - Setup public IP untuk database server
   - Setup firewall yang hanya mengizinkan IP Laravel Cloud
   - **PENTING:** Gunakan SSL/TLS connection untuk keamanan

2. **Whitelist IP Laravel Cloud:**
   - Dapatkan IP address server Laravel Cloud
   - Tambahkan ke firewall database SIMRS

#### Opsi C: SSH Tunnel (Alternative)

1. **Setup SSH Tunnel:**
   - Gunakan jump server/bastion host
   - Setup SSH tunnel dari Laravel Cloud ke database SIMRS
   - Konfigurasi aplikasi untuk menggunakan tunnel

### 3.3 Skenario 3: Database SIMRS di Laravel Cloud

Jika Anda juga deploy database SIMRS di Laravel Cloud:

1. **Gunakan Internal Network:**
   - Database di Laravel Cloud bisa diakses via internal network
   - Gunakan internal hostname yang diberikan Laravel Cloud
   - Tidak perlu setup firewall tambahan

2. **Konfigurasi:**
   ```
   SIMRS_DB_HOST=<internal_hostname_dari_laravel_cloud>
   ```

---

## Test Koneksi

### 4.1 Test dari SSH Terminal Laravel Cloud

1. **Akses SSH Terminal:**
   - Di Laravel Cloud dashboard, klik tab **"SSH"** atau **"Terminal"**
   - Atau gunakan command:
     ```bash
     laravel cloud ssh
     ```

2. **Test dengan Tinker:**
   ```bash
   php artisan tinker
   ```
   
   Kemudian di dalam tinker:
   ```php
   DB::connection('simrs')->getPdo();
   ```
   
   Jika berhasil, akan menampilkan:
   ```
   => PDO {#1234 ...}
   ```

3. **Test dengan Command:**
   ```bash
   php artisan simrs:test-connection
   ```
   
   Output yang diharapkan:
   ```
   Testing connection to SIMRS database...
   ✓ Successfully connected to SIMRS database
   Fetching sample data...
   ✓ Fetched 5 master barang records
   ```

### 4.2 Test Query Sederhana

Di SSH terminal, test query sederhana:

```bash
php artisan tinker
```

```php
// Test query ke tabel master_barang
DB::connection('simrs')->table('master_barang')->limit(5)->get();

// Test query ke tabel harga_jual
DB::connection('simrs')->table('harga_jual')->limit(5)->get();
```

### 4.3 Test dari Web Interface

1. Login ke aplikasi KMKB
2. Akses menu **SIMRS** > **Test Connection**
3. Atau akses endpoint: `/api/simrs/test-connection`
4. Pastikan response menunjukkan koneksi berhasil

---

## Troubleshooting

### 5.1 Error: SQLSTATE[HY000] [2002] Connection refused

**Penyebab:** Database SIMRS tidak bisa diakses dari Laravel Cloud.

**Solusi:**
1. Periksa `SIMRS_DB_HOST` dan `SIMRS_DB_PORT` sudah benar
2. Pastikan database SIMRS bisa diakses dari internet
3. Periksa firewall/security group mengizinkan koneksi dari IP Laravel Cloud
4. Test koneksi dari server lain untuk memastikan database accessible

### 5.2 Error: SQLSTATE[HY000] [1045] Access denied

**Penyebab:** Username atau password salah.

**Solusi:**
1. Periksa `SIMRS_DB_USERNAME` dan `SIMRS_DB_PASSWORD` di environment variables
2. Pastikan user memiliki permission untuk akses database
3. Test koneksi dengan credentials yang sama dari tool lain (phpMyAdmin, MySQL Workbench)
4. Pastikan user tidak expired atau locked

### 5.3 Error: SQLSTATE[HY000] [1049] Unknown database

**Penyebab:** Nama database salah atau database tidak ada.

**Solusi:**
1. Periksa `SIMRS_DB_DATABASE` sudah benar
2. Pastikan database benar-benar ada di server SIMRS
3. List semua database untuk memastikan nama yang benar:
   ```sql
   SHOW DATABASES;
   ```

### 5.4 Error: Connection timeout

**Penyebab:** Network issue atau firewall blocking.

**Solusi:**
1. Periksa network connectivity dari Laravel Cloud ke database SIMRS
2. Pastikan firewall mengizinkan koneksi dari IP Laravel Cloud
3. Periksa apakah database server sedang down
4. Jika menggunakan VPN, pastikan VPN connection aktif

### 5.5 Error: SSL connection required

**Penyebab:** Database SIMRS memerlukan SSL connection.

**Solusi:**
1. Tambahkan environment variable untuk SSL:
   ```
   SIMRS_MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
   ```
2. Atau disable SSL requirement di database (tidak disarankan untuk production)
3. Pastikan certificate file tersedia di server Laravel Cloud

### 5.6 Database SIMRS di Network Internal

Jika database SIMRS berada di network internal dan tidak bisa diakses langsung:

**Solusi:**
1. **Setup VPN:**
   - Hubungi Laravel Cloud support untuk setup VPN
   - Atau gunakan service VPN yang kompatibel

2. **Gunakan Proxy/Jump Server:**
   - Setup proxy server yang bisa diakses dari internet
   - Proxy akan forward request ke database internal

3. **Deploy Database di Cloud:**
   - Pertimbangkan untuk migrate database SIMRS ke cloud
   - Atau setup replica database di cloud

---

## Checklist Setup SIMRS Database

Gunakan checklist ini untuk memastikan semua langkah sudah dilakukan:

### Persiapan
- [ ] Sudah mendapatkan kredensial dari Administrator SIMRS
- [ ] Sudah membuat user read-only untuk keamanan
- [ ] Sudah memastikan network access dari Laravel Cloud ke database SIMRS

### Konfigurasi Laravel Cloud
- [ ] `SIMRS_DB_HOST` sudah di-set
- [ ] `SIMRS_DB_PORT` sudah di-set (default: 3306)
- [ ] `SIMRS_DB_DATABASE` sudah di-set
- [ ] `SIMRS_DB_USERNAME` sudah di-set
- [ ] `SIMRS_DB_PASSWORD` sudah di-set
- [ ] Semua variables sudah di-save

### Test Koneksi
- [ ] Test koneksi dari SSH terminal berhasil
- [ ] Test query sederhana berhasil
- [ ] Test dari web interface berhasil
- [ ] Tidak ada error di application logs

### Keamanan
- [ ] Menggunakan user read-only
- [ ] Password kuat dan aman
- [ ] Firewall sudah dikonfigurasi dengan benar
- [ ] SSL connection digunakan (jika memungkinkan)

---

## Tips dan Best Practices

### 1. Monitoring

- Monitor koneksi database SIMRS secara berkala
- Setup alert jika koneksi gagal
- Log semua query untuk audit trail

### 2. Performance

- Gunakan connection pooling jika memungkinkan
- Cache data yang sering diakses
- Optimize query untuk mengurangi load ke database SIMRS

### 3. Backup Strategy

- Pastikan database SIMRS memiliki backup strategy
- Pertimbangkan untuk sync data ke database lokal untuk redundancy

### 4. Documentation

- Dokumentasikan semua kredensial dengan aman
- Simpan informasi koneksi di password manager
- Update dokumentasi jika ada perubahan

---

## Support

Jika mengalami masalah yang tidak bisa diselesaikan:

1. **Check Laravel Cloud Logs:**
   - Application logs
   - Deployment logs
   - Error logs

2. **Contact Support:**
   - Laravel Cloud Support: [support@laravel.com](mailto:support@laravel.com)
   - Administrator SIMRS untuk masalah database access

3. **Community:**
   - Laravel Forums
   - Laravel Discord

---

**Selamat! Kredensial database SIMRS sudah berhasil dikonfigurasi! 🎉**

