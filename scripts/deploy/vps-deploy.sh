#!/usr/bin/env bash
#
# Deploy KMKB di VPS — dijalankan dari root repo (/www/wwwroot/kmkb).
# Dipanggil oleh /home/deploy/bin/deploy-kmkb.sh setelah git sync.
#
set -Eeuo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$APP_DIR"

export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
export COMPOSER_NO_INTERACTION=1

HEALTH_URL="${HEALTH_URL:-}"
HEALTH_TRIES="${HEALTH_TRIES:-10}"
HEALTH_DELAY="${HEALTH_DELAY:-3}"
BACKUP_DIR="${BACKUP_DIR:-/var/lib/kmkb/deploy/db-backups}"
BACKUP_RETENTION="${BACKUP_RETENTION:-10}"
AUTO_DB_RESTORE="${AUTO_DB_RESTORE:-0}"

BACKUP_FILE=""

echo "Deploy user: $(whoami)"
echo "APP_DIR: $APP_DIR"
echo "Started at: $(date -u +%FT%TZ)"

# Diset oleh deploy-kmkb.sh sebelum git sync; fallback untuk jalankan manual.
OLD_REF="${OLD_REF:-$(git rev-parse HEAD 2>/dev/null || echo none)}"

load_nvm() { [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh" && return 0; return 1; }

node_ok_for_vite() {
    command -v node >/dev/null 2>&1 || return 1
    node -e 'const p=process.version.slice(1).split(".").map(Number);const a=p[0]||0,b=p[1]||0;process.exit((a>=20)?0:1);'
}

ensure_vite_node() {
    load_nvm || true
    node_ok_for_vite && return 0
    if load_nvm; then
        nvm use 22 >/dev/null 2>&1 || nvm use 20 >/dev/null 2>&1 || true
    fi
    node_ok_for_vite
}

vite_build() {
    if ! ensure_vite_node; then
        echo "ERROR: Node 20+ diperlukan untuk Vite."
        return 1
    fi
    echo "node: $(node -v) | npm: $(npm -v)"
    if [ ! -d node_modules ] || changed_match '^package-lock\.json$'; then
        echo ">> npm ci"
        npm ci
    else
        echo ">> skip npm ci (package-lock.json tidak berubah)"
    fi
    echo ">> npm run build"
    npm run build
    chmod -R ug+rwX public/build 2>/dev/null || true
}

env_get() {
    grep -E "^$1=" "$APP_DIR/.env" 2>/dev/null | tail -n1 | cut -d= -f2- | sed 's/^"//; s/"$//; s/^'"'"'//; s/'"'"'$//'
}

db_backup() {
    local conn host port name user pass ts
    conn="$(env_get DB_CONNECTION)"; conn="${conn:-mysql}"
    [ "$conn" = "mysql" ] || { echo ">> lewati backup DB (bukan mysql)"; return 0; }
    command -v mysqldump >/dev/null 2>&1 || { echo "ERROR: mysqldump tidak ada"; return 1; }

    host="$(env_get DB_HOST)"; host="${host:-127.0.0.1}"
    port="$(env_get DB_PORT)"; port="${port:-3306}"
    name="$(env_get DB_DATABASE)"
    user="$(env_get DB_USERNAME)"; user="${user:-root}"
    pass="$(env_get DB_PASSWORD)"

    [ -n "$name" ] || { echo "ERROR: DB_DATABASE kosong"; return 1; }

    mkdir -p "$BACKUP_DIR"
    ts="$(date -u +%Y%m%dT%H%M%SZ)"
    BACKUP_FILE="${BACKUP_DIR}/${name}_${ts}.sql.gz"
    echo ">> backup DB '${name}'"
    MYSQL_PWD="$pass" mysqldump \
        --host="$host" --port="$port" --user="$user" \
        --single-transaction --quick --routines --triggers \
        --no-tablespaces "$name" | gzip -c > "$BACKUP_FILE"
    ls -1t "${BACKUP_DIR}/${name}_"*.sql.gz 2>/dev/null \
        | tail -n +"$((BACKUP_RETENTION + 1))" | xargs -r rm -f
}

health_check() {
    local url="$1" code i
    for i in $(seq 1 "$HEALTH_TRIES"); do
        code="$(curl -fsS -o /dev/null -w '%{http_code}' --max-time 10 "$url" 2>/dev/null || echo 000)"
        if [ "$code" = "200" ]; then
            echo "Health OK ($url)"
            return 0
        fi
        echo "Health $i/$HEALTH_TRIES: HTTP $code"
        sleep "$HEALTH_DELAY"
    done
    return 1
}

rollback() {
    echo "!!!!! ROLLBACK ke ${OLD_REF} !!!!!"
    [ "$OLD_REF" != "none" ] && git reset --hard "$OLD_REF" || true
    composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader || true
    vite_build || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
}

on_error() {
    trap - ERR
    rollback
    exit 1
}
trap on_error ERR

CHANGED="ALL"
changed_match() {
    [ "$CHANGED" = "ALL" ] && return 0
    printf '%s\n' "$CHANGED" | grep -qE "$1"
}

mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs storage/app/public/{hospitals,references}
chmod -R ug+rwX storage bootstrap/cache 2>/dev/null || true

if [ ! -f .env ]; then
    echo "ERROR: .env belum ada. Salin dari .env.example dan isi DB/APP_KEY di server."
    exit 1
fi

if [ ! -d vendor ] || changed_match '^composer\.(lock|json)$'; then
    echo ">> composer install"
    composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
else
    echo ">> skip composer install"
fi

FRONTEND_RE='(\.blade\.php$|\.css$|\.js$|^package(-lock)?\.json$|vite\.config|tailwind|postcss)'
NEEDS_BUILD=0
if [ ! -d public/build ] || [ ! -d node_modules ]; then NEEDS_BUILD=1
elif changed_match "$FRONTEND_RE"; then NEEDS_BUILD=1; fi

if [ "$NEEDS_BUILD" -eq 1 ]; then vite_build; else echo ">> skip frontend build"; fi

db_backup
php artisan migrate --force

if [ ! -L public/storage ]; then
    php artisan storage:link || true
fi

php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true
chmod -R ug+rwX storage/framework storage/logs bootstrap/cache 2>/dev/null || true

if [ -n "$HEALTH_URL" ]; then
    health_check "$HEALTH_URL" || { trap - ERR; rollback; exit 1; }
fi

echo "KMKB deploy done at $(date -u +%FT%TZ)"
