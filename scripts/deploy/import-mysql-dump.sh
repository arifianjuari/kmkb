#!/usr/bin/env bash
# Import dump dari Laravel Cloud ke DB lokal VPS.
# Usage: ./scripts/deploy/import-mysql-dump.sh /path/to/backup.sql.gz
set -euo pipefail

DUMP="${1:?Berikan path file .sql atau .sql.gz}"
APP_DIR="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$APP_DIR"

if [ ! -f .env ]; then
    echo "ERROR: .env tidak ditemukan di $APP_DIR"
    exit 1
fi

DB_HOST=$(grep -E '^DB_HOST=' .env | tail -1 | cut -d= -f2- | tr -d '"')
DB_PORT=$(grep -E '^DB_PORT=' .env | tail -1 | cut -d= -f2- | tr -d '"')
DB_DATABASE=$(grep -E '^DB_DATABASE=' .env | tail -1 | cut -d= -f2- | tr -d '"')
DB_USERNAME=$(grep -E '^DB_USERNAME=' .env | tail -1 | cut -d= -f2- | tr -d '"')
DB_PASSWORD=$(grep -E '^DB_PASSWORD=' .env | tail -1 | cut -d= -f2- | tr -d '"')

DB_PORT="${DB_PORT:-3306}"

echo ">> Import ke database ${DB_DATABASE}@${DB_HOST}"

if [[ "$DUMP" == *.gz ]]; then
    gunzip -c "$DUMP" | MYSQL_PWD="$DB_PASSWORD" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" "$DB_DATABASE"
else
    MYSQL_PWD="$DB_PASSWORD" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" "$DB_DATABASE" < "$DUMP"
fi

echo ">> Selesai. Jalankan: php artisan migrate --force (jika perlu)"
