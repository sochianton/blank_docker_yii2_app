#!/bin/bash

PATH=/bin:/sbin:/usr/bin:/usr/sbin:/root
PATH=$PATH:/usr/bin:/usr/sbin:/usr/local/bin:/usr/local/sbin



#ROOT=$(pwd)
ROOT=`dirname $0`
CONF_DIR=$ROOT/docker
RUN=$CONF_DIR/run

MAIN=$ROOT/docker-compose.yml
CONFIG=$CONF_DIR/project-config.yml
CONNECT="-f $MAIN -f $CONFIG"

function echoYell() {
    echo -e "\033[0;33m$1\033[0m"
}
function echoBlue() {
    echo -e "\033[0;34m$1\033[0m"
}


function ShowHelp {
    echoYell 'Help'
    echoBlue 'Usage:'
    echoYell '     project.sh COMMAND [keys]'
    echoYell ''
    echoBlue 'Commands:'
    echoYell '  install|update                                  Работа с composer'
    echoYell '  down|up|stop|restart|build|pull|logs|rm|run     Работа с docker-compose проекта'
    echoYell '  remove                                          Остановить Docker сервис и удалить компоненты Docker (values, containers, networks и images)'
    echoYell '  analyze_diff                                    Проверка измененных файлов PHPStan'
    echoYell '  analyze_phpstan                                 Проверка PHPStan указанных файлов'
    echoYell '  analyze_phpmd                                   Проверка PHPMd указанных файлов'
    echoYell '  web                                             Выполнить PHP script на backend'
    echoYell '  exec                                            Подключиться к контейнеру по имени в compose'
}

function Dependencies {
    # use current dev image with fxp plugin (for bower)
    # composer cache dir
    mkdir -p $RUN/cache

    docker run --rm -v $ROOT:/app -v $RUN/cache:/composer/cache --user $(stat -c '%u' ./):$(stat -c '%g' ./) \
    gitlab.icerockdev.com:4567/docker/dev-alpine-php-7.3:latest composer $@ --prefer-dist
}

function ComposeCommand {
    docker-compose $CONNECT $@
}

function ComposeCommandExec {
    source $ROOT/.env
    if [ -t 0 ];
    then USE_TTY="";
    else USE_TTY="-T";
    fi

    ComposeCommand exec ${USE_TTY} --user $(stat -c '%u' ./):$(stat -c '%g' ./) $@
}

function ComposeCommandNoUser {
    source $ROOT/.env
    if [ -t 0 ];
    then USE_TTY="";
    else USE_TTY="-T";
    fi

    ComposeCommand exec ${USE_TTY} $@
}

function DiffCodeAnalyze {
    STAGED_FILES=`git diff --name-only --diff-filter=ACMR HEAD | grep \\.php`
    docker run -i --user $(id -u):$(id -g) --env FILES="$STAGED_FILES" --rm  -v=$(pwd)/:/app \
    gitlab.icerockdev.com:4567/scl/docker-image/code-analyzer:latest php /scripts/phpstan.phar analyse -l 4 \
    -c /config/phpstan.neon $STAGED_FILES
}

function PhpStanAnalyze {
    docker run -i --user $(id -u):$(id -g) --rm  -v=$(pwd)/:/app \
    gitlab.icerockdev.com:4567/scl/docker-image/code-analyzer:latest php /scripts/phpstan.phar analyse -l 4 \
    -c /config/phpstan.neon $@
}

function PhpMdAnalyze {
    docker run -i --user $(id -u):$(id -g) --rm  -v=$(pwd)/:/app \
    gitlab.icerockdev.com:4567/scl/docker-image/code-analyzer:latest php /scripts/phpmd.phar $@ text /config/phpmd.xml
}

function Backup {

    echo ""
    echo ""
    echo ""
    source $ROOT/.env

    PREFIX=`date +%F` #Префикс по дате для создания резервной копий
    POSTFIX=`date +%d-%m-%Y"_"%H-%M-%S`
    FILE=${BACKUP_DIR}/${PREFIX}/backup-${POSTFIX}.sql.gz


    echo "[--------------------------------[${PREFIX}]--------------------------------]"
    #echo ${DB_USER}
    #echo ${DB_NAME}
    echo "[`date +%F_%H-%M-%S`] Run the backup script..."
    mkdir -p ${BACKUP_DIR}/${PREFIX} #2> /dev/null.
    echo "[`date +%F_%H-%M-%S`] Generate a database backup..."

    PGPASSWORD=${POSTGRES_PASSWORD}
    export PGPASSWORD
    pg_dump -h localhost -p 5432 -U ${DB_USER} -w --create --inserts ${DB_NAME} | gzip > ${FILE}
    unset PGPASSWORD

    #ComposeCommand exec -T -u ${DB_USER}:${DB_USER} postgres sh -c ${SQL} | gzip > ${FILE}
    echo "[`date +%F_%H-%M-%S`] Generate a database backup done"

    exit 0
}

function run() {
    COMMAND=$1
    case "$COMMAND" in
        install|update|require)
            echo "$COMMAND  Project"
            Dependencies $@
        ;;
        remove)
            echo 'Remove Project'
            ComposeCommand down -v --rmi local
        ;;
        down|up|stop|restart|build|pull|logs|rm|run)
            ComposeCommand $@
        ;;
        web)
            shift
            echo 'Exec PHP script'
            ComposeCommandExec web $@
        ;;
        exec)
            shift
            ComposeCommandExec $@
        ;;
        execnouser)
            shift
            ComposeCommandNoUser $@
        ;;
        swagger)
            ComposeCommandExec web php yii swagger
        ;;
        analyze_diff)
            shift
            DiffCodeAnalyze
        ;;
        analyze_phpstan)
            shift
            PhpStanAnalyze $@
        ;;
        analyze_phpmd)
            shift
            PhpMdAnalyze $@
        ;;
        backup)
            Backup
        ;;
    esac
}

run $@
