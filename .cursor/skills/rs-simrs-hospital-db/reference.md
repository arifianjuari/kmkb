# Referensi SIMRS — jaringan, skema, query

## Jaringan

| Skenario | MySQL langsung |
|----------|----------------|
| Server di RS / VPN aktif | Host internal, mis. `172.16.1.251:3306` |
| VPS cloud tanpa VPN | Gagal — pasang OpenVPN di VPS atau API HTTPS |
| Laptop dev lokal | VPN client ke RS untuk uji |

Split tunnel umum: `172.16.1.0/24`. Dokumentasi operasional Faskelola: `docs/operations/koneksi-rawat-jalan.md`, `koneksi-db-praktekobgin-rawat-inap.md`, `vps-vpn-simrs-setup.md`.

## Parameter database

| Parameter | Nilai tipikal |
|-----------|----------------|
| Engine | MySQL |
| Database | `simsvbaru` |
| User | `web_hasta` (read-only) |
| Port | `3306` |

## Rawat jalan (Ralan) — hari ini

Konstanta: `OUTPATIENT_STATUS = 'Ralan'`.

```sql
SELECT DISTINCT
  reg.no_rawat, reg.no_reg, reg.stts, reg.tgl_registrasi,
  pasien.no_rkm_medis, pasien.nm_pasien, pasien.no_ktp, pasien.tgl_lahir,
  pasien.jk, pasien.alamat, pasien.no_tlp,
  poli.nm_poli, poli.kd_poli,
  dokter.nm_dokter, dokter.kd_dokter
FROM reg_periksa AS reg
INNER JOIN pasien ON reg.no_rkm_medis = pasien.no_rkm_medis
INNER JOIN poliklinik AS poli ON reg.kd_poli = poli.kd_poli
INNER JOIN dokter ON reg.kd_dokter = dokter.kd_dokter
WHERE DATE(reg.tgl_registrasi) = CURDATE()
  AND reg.status_lanjut = 'Ralan'
  AND reg.stts != 'Batal'
  AND (
    pasien.nm_pasien LIKE :like
    OR pasien.no_rkm_medis LIKE :like
    OR pasien.no_ktp LIKE :like
    OR reg.no_rawat LIKE :like
  )
ORDER BY reg.no_reg
LIMIT 20;
```

## Rawat inap aktif (Ranap)

Filter: `kamar_inap.stts_pulang = '-'`.

```sql
SELECT DISTINCT
  ran.no_rawat,
  pasien.no_rkm_medis, pasien.nm_pasien, pasien.no_ktp, pasien.tgl_lahir,
  pasien.jk, pasien.alamat, pasien.no_tlp,
  bngsal.nm_bangsal, ran.kd_kamar, ran.tgl_masuk,
  dokter.nm_dokter, dokter.kd_dokter
FROM kamar_inap AS ran
LEFT JOIN reg_periksa AS reg ON ran.no_rawat = reg.no_rawat
LEFT JOIN pasien ON reg.no_rkm_medis = pasien.no_rkm_medis
LEFT JOIN kamar ON ran.kd_kamar = kamar.kd_kamar
LEFT JOIN bangsal AS bngsal ON kamar.kd_bangsal = bngsal.kd_bangsal
LEFT JOIN dpjp_ranap AS dpjp ON ran.no_rawat = dpjp.no_rawat
LEFT JOIN dokter ON dpjp.kd_dokter = dokter.kd_dokter
WHERE ran.stts_pulang = '-'
  AND (
    pasien.nm_pasien LIKE :like
    OR pasien.no_rkm_medis LIKE :like
    OR pasien.no_ktp LIKE :like
  )
ORDER BY bngsal.nm_bangsal, ran.tgl_masuk DESC
LIMIT 20;
```

## Response JSON search (contoh bentuk)

```json
{
  "no_rawat": "2026/01/15/000001",
  "no_rkm_medis": "123456",
  "full_name": "Nama Pasien",
  "identity_number": "3276012345670001",
  "birth_date": "1990-01-01",
  "no_reg": "001",
  "nm_poli": "Kandungan",
  "nm_dokter": "dr. X",
  "stts": "Belum",
  "visit_type": "ralan"
}
```

`visit_type`: `ralan` | `ranap` bila menggabungkan pencarian admission.

## Cache key ketersediaan

Pola Faskelola:

```text
emr.hospital_sims.available:{host}:{port}
```

TTL default 30 detik. Invalidate saat search gagal (connection error).

## Troubleshooting

| Gejala | Kemungkinan |
|--------|-------------|
| Connection refused | VPN mati, host salah, firewall |
| Timeout | `HOSPITAL_DB_CONNECT_TIMEOUT` terlalu besar/small; jaringan lambat |
| Access denied | User/password salah atau host MySQL tidak mengizinkan IP client |
| configured=yes, available=no | Env OK tapi jaringan tidak sampai ke 172.16.1.251 |
| Hasil kosong | Bukan hari ini (rajal), pasien sudah pulang (ranap), atau status Batal |
