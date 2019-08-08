# Euroservice
````
#### Info
- All variables required for replace before project start
- You can replace by IDE replace in project. Example: replace `${project@domain.com} => admin@mydomain.com`

### Prepare local DNS

Add following lines to `/etc/hosts`:

```bash
127.0.0.19 api.euroservice.local admin.euroservice.local
```

### Prepare docker-compose config
Copy `docker/local/project-config.yml` or `docker/dev/project-config.yml` to ``docker/project-config.yml`` and set necessary env vars:

#### Config details
- check system user id (call command `id` in bash) and set actual value to web and queue sections (docker/project-config.yml): `user: "1000:1000"`

### Pulling docker images
````bash
./project.sh pull
````
### Install project dependencies:
```bash
./project.sh install
```

#### Details
- NOTICE: for some images you need login in our gitlab registry: `docker login gitlab.icerockdev.com:4567`
- ./project.sh is script for easy usage docker-compose with multiple files. Its proxy docker-compose commands and add special commands (like install/update for composer)
- Yo can see available commands: `./project.sh help`, or open file and see directly :)

### Start app for development
````bash
./project.sh up -d
````

# Deploy options

## Автоматический бэкап

После деплоя на прод-сервер, вызывается команда создания бекапа БД.
Конфигурация пути хранения задается тут `docker/prod/.env`.
В данном примере работает с `Postgres`. Для остальных СУБД нужно изменить команду для бекапа (тут `docker/dev/db-backup.sh`).
 Так же не забыть добавить крон задачу на хост-машине:
 ```bash
30 */6 * * * /var/www/project/db-backup.sh
```

## Конфигурация ssl

Вся работа с сертификатами ведется в файлах `project-compose.yml` и `nginx.conf` в папке окружения.
При использовании letsencrypt сами сертификаты и каталог для проверки располагать там же.

## Настройка ENV

Для **local** версии в конфигурации подключений используем конструкции вида `$_ENV['POSTGRESL_HOST']`. Это дает гибкость
в конфигурировании приложения с помощью docker.
В настройках php обязательно разрешаем чтение env.

## Cron example
TODO: create folder `/var/www/log/`
````bash
 * * * * * (date && cd /var/www/euroservice && ./project.sh web php yii control/action) >> /var/www/log/control_action.log
````

## CI SETTINGS

Set variables in CI settings

```
-- DB
DEV_DB_PASSWORD - pass
DEV_DB_ROOT - root pass

-- Copy for prod env with prefix PROD
DEV_SSH_DEPLOY_KEY - deploy ssh key
DEV_REMOTE_PORT - deploy port
DEV_REMOTE_HOST - deploy host
DEV_REMOTE_USER - deploy user
DEV_REMOTE_DIR - deploy directory


```

## CI server setup

```
-- allow www-data login
usermod -s /bin/bash www-data
-- connect to docker via www-data - need restart active sessions via user (if you work as root do nothing)
usermod -aG docker www-data
-- add rights for www-data
chown www-data. /var/www/
-- change acc to www-data
su www-data
-- generate deploy keys
ssh-keygen -t rsa -C "deploy@veka.com" -b 4096
-- add key as authorized
cat /var/www/.ssh/id_rsa.pub > /var/www/.ssh/authorized_keys

-- Copy id_rsa content and paste to CI variable (DEV_SSH_DEPLOY_KEY) as protected
-- Set other variables in Settings
```

## Генерация swagger
Для генерации файла документации API нужно выполнить команду:

```
./project.sh swagger
```
Базовые анотации находятся в файле `api/config/bootstrap.php`

Пример использования в контроллере:
```
    /**
     * @OA\Get(
     *     tags={"auth"},
     *     path="/v1/login",
     *     summary="Get token by phone",
     *     @OA\Parameter(in="query",name="phone",required=true, @OA\Schema(type="string", example="test@test.com")),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Ok",
     *         @OA\JsonContent(ref="#/components/schemas/TokenResponse")
     *     ),
     *     @OA\Response(response="500", description="Can`t create customer for unknown reason"),
     *     @OA\Response(response="422", description="This phone number is suspended")
     * )
     * @return TokenRequest|TokenResponse
     * @throws \Throwable
     * @throws SafeException
     * @throws Exception
     */
    public function actionToken()
    {
    //...
```

## Twilio
Конфигурация в файле `common/config/main-local.php`
```
'Yii2Twilio' => [
    'class' => filipajdacic\yiitwilio\YiiTwilio::class,
    'account_sid' => 'YOUR_TWILIO_ACCOUNT_SID_HERE',
    'auth_key' => 'YOUR_TWILIO_AUTH_KEY_HERE',
],
```
Использование в очереди:
```
/** @var User $user */
/** @var string $message */
Yii::$app->queue->push(new SendSmsJob([
    'user' => $user,
    'message' => $message,
]));
```
Использование напрямую:
```
/** @var User $user */
/** @var string $message */
Yii::$app->notifierSMS->sendMessageByUser($user, $message);
```
