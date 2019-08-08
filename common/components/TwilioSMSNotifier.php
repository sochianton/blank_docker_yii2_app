<?php

namespace common\components;

use common\models\User;
use filipajdacic\yiitwilio\YiiTwilio;
use scl\tools\rest\exceptions\SafeException;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;
use Yii;

/**
 * Class TwilioSMSNotifier
 */
class TwilioSMSNotifier extends YiiTwilio
{
    // Заглушка для проверки
    protected $default_phone = null;

    /**
     * @throws \Exception
     */
    public function init()
    {
        $this->account_sid = Yii::$app->params['Twilio']['account_sid'];
        $this->auth_key = Yii::$app->params['Twilio']['auth_key'];
        parent::init();
    }

    /**
     * @param $phone
     * @param $message
     * @throws SafeException
     */
    public function sendMessageByPhone($phone, $message)
    {
        return $this->send($this->default_phone ?? $phone, $message);
    }

    /**
     * @param string $phone
     * @param string $message
     * @throws SafeException
     */
    protected function send(string $phone, string $message)
    {
        $phone = "+" . (strlen($phone) === 10 ? '7' : '') . $phone;
        try {
            /** @var Client $client */
            $client = $this->initTwilio();
            /** @var MessageList $messages */
            $messages = $client->account->messages;
            /** @noinspection MissedFieldInspection */
            $messages->create(
                $phone,
                [
                    "body" => $message,
                    "from" => Yii::$app->params['Twilio']['from']
                ]
            );
        } catch (RestException $e) {
            Yii::warning($e->getMessage());
            throw new SafeException(503, $e->getMessage());
        }
    }

    /**
     * @param User $user
     * @param string $message
     * @throws SafeException
     */
    public function sendMessageByUser(User $user, string $message)
    {
        $phone = $user->phone;
        return $this->send($this->default_phone ?? $phone, $message);
    }
}
