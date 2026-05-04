#!/bin/sh
# Note: intentionally NOT using 'set -e' so individual step failures
# (like migration SSL issues) won't kill the whole container

echo "🚀 Starting HRIS Aratech..."

# ─── Wait for MySQL to be ready ───────────────────────────────────────────────
echo "⏳ Waiting for database connection..."
until php -r "
    \$conn = @new mysqli(
        getenv('DB_HOST') ?: 'db',
        getenv('DB_USERNAME') ?: 'hris_user',
        getenv('DB_PASSWORD') ?: 'hris_secret',
        getenv('DB_DATABASE') ?: 'hrappsprod',
        intval(getenv('DB_PORT') ?: 3306)
    );
    if (\$conn->connect_error) exit(1);
    exit(0);
"; do
    echo "   Database not ready yet, retrying in 3s..."
    sleep 3
done
echo "✅ Database connected!"

# ─── Generate APP_KEY jika belum ada ──────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "🔑 APP_KEY kosong di environment, generate dari artisan..."
    # Generate key langsung ke .env agar persistent
    php artisan key:generate --force --no-interaction || echo "⚠️  key:generate failed (mungkin .env readonly), lanjut..."
else
    echo "🔑 APP_KEY sudah ada: OK"
fi

# ─── Clear caches ─────────────────────────────────────────────────────────────
echo "🧹 Clearing caches..."
mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

su -s /bin/sh www-data -c "php artisan config:clear" || true
su -s /bin/sh www-data -c "php artisan route:clear" || true
su -s /bin/sh www-data -c "php artisan view:clear" || true

# ─── Run migrations ───────────────────────────────────────────────────────────
echo "🗄️  Running migrations..."

# Disable SSL verification untuk koneksi mysql CLI
export MYSQL_PWD=hris_secret
export LIBMYSQL_ENABLE_CLEARTEXT_PLUGIN=1

# Tulis my.cnf untuk disable SSL pada mysql CLI
cat > /root/.my.cnf <<'MYCNF'
[client]
ssl_mode=disabled
ssl-verify-server-cert=OFF
MYCNF

# Jalankan migrate, abaikan error (misal jika sudah ter-migrate)
php artisan migrate --force --no-interaction 2>&1 || echo "⚠️  Migrate gagal atau sudah ter-migrate, lanjut..."

# ─── Create storage symlink ───────────────────────────────────────────────────
echo "🔗 Creating storage symlink..."
mkdir -p storage/app/public/claims/attachments
chown -R www-data:www-data storage/app/public 2>/dev/null || true
rm -f public/storage
php artisan storage:link --force 2>/dev/null || true

echo "✅ Setup complete! Starting PHP-FPM..."

# ─── Start PHP-FPM ────────────────────────────────────────────────────────────
exec php-fpm
