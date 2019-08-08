<?php

namespace scl\yii\push\job;

use paragraph1\phpFCM\Notification;
use scl\yii\push\Push;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class PushTokenJob
 * @package scl\yii\push\job
 */
final class PushTokenJob implements JobInterface
{
    /** @var string[] $tokenList */
    private $tokenList;
    /** @var Notification $note */
    private $note;
    /** @var array $data */
    private $data;
    /** @var string $className */
    private $className;

    /**
     * PushTokenJob constructor.
     * @param string[] $tokenList
     * @param Notification $note
     * @param array $data
     * @param string $className
     */
    public function __construct(
        array $tokenList,
        Notification $note,
        array $data = [],
        $className = Push::class
    ) {
        $this->tokenList = $tokenList;
        $this->note = $note;
        $this->data = $data;
        $this->className = $className;
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     * @throws ErrorException
     */
    public function execute($queue)
    {
        $container = Yii::$container;

        /** @var Push $pushService */
        try {
            $pushService = $container->get($this->className);
        } catch (NotInstantiableException | InvalidConfigException $e) {
            Yii::error('Invalid service configuration: ' . $e->getMessage());
            throw new ErrorException($e->getMessage());
        }

        if (!empty($this->tokenList)) {
            $pushService->sendToTokens($this->tokenList, $this->note, $this->data);
        }
    }
}
