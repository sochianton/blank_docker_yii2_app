#!/bin/bash
set -e

while true; do
  php -r "@fsockopen('$RABBITMQ_HOST', $RABBITMQ_PORT) or exit(1); exit(0);" && echo "[$(date)] Connected to RabbitMQ" && break
  echo "[$(date)] Waiting RabbitMQ connection"
  sleep 1
done

php yii queue/listen -v
