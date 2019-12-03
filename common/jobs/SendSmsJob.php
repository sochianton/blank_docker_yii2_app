<?php

namespace common\jobs;

use common\components\TwilioSMSNotifier;
use common\ar\User;
use scl\tools\rest\exceptions\SafeException;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class SendSmsJob extends BaseObject implements JobInterface
{
    /** @var User $user */
    public $user;
    /** @var string $message */
    public $message;

    /**
     * @param Queue $queue
     * @return void
     * @throws SafeException
     */
    public function execute($queue)
    {
        /** @var TwilioSMSNotifier $sender */
        $sender = Yii::$app->notifierSMS;

        $sender->sendMessageByUser($this->user, $this->message);
    }
}
