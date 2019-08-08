#!/bin/bash
set -e
# init folder after mount
mkdir -p /runtime
mkdir -p /runtime/backend
mkdir -p /runtime/api
mkdir -p /runtime/console
#    for create link
rm -rf /app/console/runtime
rm -rf /app/backend/runtime
rm -rf /app/api/runtime
#    links
ln -sf /runtime/backend /app/backend/runtime
ln -sf /runtime/console /app/console/runtime
ln -sf /runtime/api /app/api/runtime
#    storage
mkdir -p /storage
ln -sf /storage /app/storage
#    change owner
chmod 777 /runtime
chmod 777 /storage

# correct rights after mount
chown $UID:$GUID /runtime
chown $UID:$GUID /runtime/backend
chown $UID:$GUID /runtime/api
chown $UID:$GUID /runtime/console
chown $UID:$GUID /storage

while true; do
  php -r "@fsockopen('$POSTGRES_HOST', 5432) or exit(1); exit(0);" && break
  echo "wait DB connection...";
  sleep 1
done

#
# Список команд для зупуска сторонних миграций
#

# Запуск штатных миграций
php /app/yii migrate --interactive=0
exec "$@"
