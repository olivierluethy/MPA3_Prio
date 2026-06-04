#!/bin/sh
# ----------------------------------------------------------------------------
# Generates the application's .env file from the container environment so that
# both phpdotenv ($_ENV) and getenv() see the same configuration. Only created
# if it does not already exist (so a bind-mounted dev .env is never clobbered).
# ----------------------------------------------------------------------------
set -e

ENV_FILE=/var/www/html/.env

if [ ! -f "$ENV_FILE" ]; then
    echo "[entrypoint] Generating $ENV_FILE from environment variables"
    cat > "$ENV_FILE" <<EOF
APP_ENV=${APP_ENV:-production}
APP_BASE_PATH=${APP_BASE_PATH:-}
APP_URL=${APP_URL:-}
DB_SERVER=${DB_SERVER:-db}
DB_NAME=${DB_NAME:-prio}
DB_USERNAME=${DB_USERNAME:-prio}
DB_PASSWORD=${DB_PASSWORD:-prio_secret}
ENCRYPTION_KEY=${ENCRYPTION_KEY:-change_me_dev_key_0123456789abcd}
EOF
    chown www-data:www-data "$ENV_FILE"
    chmod 640 "$ENV_FILE"
fi

exec "$@"
