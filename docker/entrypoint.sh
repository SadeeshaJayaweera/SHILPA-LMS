#!/bin/sh
set -e

echo "Starting SHILPA LMS Production Container..."

# Ensure required storage and cache directories exist with appropriate permissions
mkdir -p storage/app/public \
         storage/framework/cache \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Check if database is reachable before migrating
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection to $DB_HOST:$DB_PORT..."
    RETRIES=30
    until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null || [ $RETRIES -eq 0 ]; do
        echo "Waiting for database... ($RETRIES attempts remaining)"
        RETRIES=$((RETRIES-1))
        sleep 2
    done
fi

# Run database migrations
echo "Executing database migrations..."
php bin/migrate.php || echo "Migration step completed with warning."

# Execute main process command
exec "$@"
