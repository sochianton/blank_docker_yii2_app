#!/bin/bash

. ./.env 1>>/dev/null 2>&1

CONFIG="-f ./docker-compose.yml"
PREFIX=`date +%F` #Префикс по дате для создания резервной копий
POSTFIX=`date +%d-%m-%Y"_"%H-%M-%S`
FILE=${BACKUP_DIR}/${PREFIX}/backup-${POSTFIX}.sql.gz

echo "[--------------------------------[${PREFIX}]--------------------------------]"
echo "[`date +%F_%H-%M-%S`] Run the backup script..."
mkdir -p ${BACKUP_DIR}/${PREFIX} #2> /dev/null.
echo "[`date +%F_%H-%M-%S`] Generate a database backup..."
/usr/local/bin/docker-compose ${CONFIG} exec -T postgres sh -c "pg_dump -U ${DB_USER} ${DB_NAME}" | gzip > ${FILE}
echo "[`date +%F_%H-%M-%S`] Generate a database backup done"

exit 0
