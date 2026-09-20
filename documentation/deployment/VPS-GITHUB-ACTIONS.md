# Deploy KMKB ke VPS (GitHub Actions)

## Lokasi di server

| Path | Fungsi |
|------|--------|
| `/www/wwwroot/kmkb` | Kode aplikasi (clone `arifianjuari/kmkb`) |
| `/home/deploy/bin/deploy-kmkb.sh` | `git pull` + jalankan `scripts/deploy/vps-deploy.sh` |
| `/home/deploy/bin/deploy-kmkb-wrapper.sh` | Lock + log deploy |
| `/var/lib/kmkb/deploy/` | Status, log, backup DB |
| Runner | `kmkb-prod` (label workflow: `self-hosted`, `kmkb-prod`) |

## Alur CI/CD

1. Push ke branch `main` di GitHub.
2. Workflow `.github/workflows/deploy.yml` jalan di runner **kmkb-prod** di VPS.
3. Wrapper memanggil `deploy-kmkb.sh` → sync `origin/main` → `scripts/deploy/vps-deploy.sh`.

## Setup pertama (sekali)

Di server, sebagai user yang deploy:

```bash
cp /www/wwwroot/kmkb/.env.example /www/wwwroot/kmkb/.env
cd /www/wwwroot/kmkb && php artisan key:generate
# Isi DB_*, SIMRS_*, APP_URL di .env
mysql -e "CREATE DATABASE kmkb_db ..."
php artisan migrate --force
```

Nginx vhost + SSL untuk domain KMKB (belum termasuk otomatis di workflow).

## Environment opsional deploy

- `HEALTH_URL` — URL health check setelah deploy (mis. `https://kmkb.example.com/up` jika route ada).
- `BACKUP_DIR`, `BACKUP_RETENTION` — backup MySQL sebelum migrate.

## Log deploy

```bash
tail -f /var/lib/kmkb/deploy/deploy.log
cat /var/lib/kmkb/deploy/deploy.status
```
