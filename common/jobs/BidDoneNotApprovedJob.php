<?php

namespace common\jobs;

use common\models\Bid;
use common\repository\BidRepository;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class BidDoneNotApprovedJob
 * @package common\jobs
 */
class BidDoneNotApprovedJob extends BaseObject implements JobInterface
{
    /** @var integer $bidId */
    public $bidId;

    /**
     * @param Queue $queue
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws Throwable
     */
    public function execute($queue)
    {
        /** @var BidRepository $bidRepository */
        $bidRepository = Yii::$container->get(BidRepository::class);

        $bid = $bidRepository->get($this->bidId);
        if ($bid && $bid->status === Bid::STATUS_CONFIRMATION) {
            $bidRepository->setStatus($bid, Bid::STATUS_COMPLETE);
        }
    }
}
