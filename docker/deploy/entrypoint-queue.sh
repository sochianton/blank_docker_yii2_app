#!/bin/bash
set -e
# init folder after mount
mkdir -p /runtime
mkdir -p /runtime/console
#    for create link
rm -rf /app/console/runtime
#    links
ln -sf /runtime/console /app/console/runtime
#    storage
mkdir -p /storage
rm -rf /app/storage
ln -sf /storage /app/storage
#    change owner
chmod 777 /runtime
chmod 777 /storage

# correct rights after mount
chown $UID:$GUID /runtime
chown $UID:$GUID /runtime/console
chown $UID:$GUID /storage

while true; do
  php -r "@fsockopen('$RABBITMQ_HOST', $RABBITMQ_PORT) or exit(1); exit(0);" && echo "[$(date)] Connected to RabbitMQ" && break
  echo "[$(date)] Waiting RabbitMQ connection"
  sleep 1
done

php yii queue/listen -v
