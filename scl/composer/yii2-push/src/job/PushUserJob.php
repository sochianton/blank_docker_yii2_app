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
 * Class PushUserJob
 * @package scl\yii\push\job
 */
final class PushUserJob implements JobInterface
{
    /** @var int[] $uidList */
    private $uidList;
    /** @var int $usersType */
    private $usersType;
    /** @var Notification $note */
    private $note;
    /** @var array $data */
    private $data;
    /** @var string $className */
    private $className;

    /**
     * PushUserJob constructor.
     * @param int[] $uidList
     * @param int $usersType
     * @param Notification $note
     * @param array $data
     * @param string $className
     */
    public function __construct(
        array $uidList,
        int $usersType,
        Notification $note,
        array $data = [],
        $className = Push::class
    ) {
        $this->uidList = $uidList;
        $this->usersType = $usersType;
        $this->note = $note;
        $this->data = $data;
        $this->className = $className;
    }

    /**
     * @param Queue $queue which pushed and is handling the job
     * @throws \Exception
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

        if (!empty($this->uidList)) {
            $pushService->sendToUsers($this->uidList, $this->usersType, $this->note, $this->data);
        }
    }
}
