#!/bin/bash
set -e
# Yii2 init
php /app/init --env=$PHP_ENV --overwrite=All
while true; do
  php -r "@fsockopen('$POSTGRES_HOST', 5432) or exit(1); exit(0);" && break
  sleep 1
done

#
# Список команд для зупуска сторонних миграций
#

# Запуск штатных миграций
php /app/yii migrate --interactive=0
exec "$@"
