<?php

namespace scl\mailer\job;

use scl\mailer\Mailer;
use Yii;
use yii\di\Instance;
use yii\queue\JobInterface;

class SendEmailJob implements JobInterface
{
    private $mailer;
    private $logCategory;
    private $data = [];

    /**
     * SendEmailJob constructor.
     * @param array $data
     * @param string $logCategory
     * @param string $mailer
     */
    public function __construct(array $data, string $logCategory, string $mailer)
    {
        $this->data = $data;
        $this->logCategory = $logCategory;
        $this->mailer = $mailer;
    }


    /**
     * @param \yii\queue\Queue $queue
     * @return bool
     */
    public function execute($queue)
    {
        $result = false;
        try {
            /** @var Mailer $mailer */
            $mailer = Instance::ensure($this->mailer, Mailer::class);

            $adapter = $mailer->adapter;
            $adapter->unserializeParams($this->data);
            $result = $adapter->postSend();
            if (!$result) {
                Yii::warning([
                    'msg' => 'FAILED - Sending email',
                    'err' => $adapter->ErrorInfo,
                ], $this->logCategory);
            }

        } catch (\Exception $e) {
            Yii::error([
                'msg' => 'FAILED - Sending email',
                'exception' => $e->getMessage(),
            ]);
        }

        return $result;
    }
}
