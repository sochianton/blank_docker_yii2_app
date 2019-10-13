<?php

namespace common\jobs;

use common\models\Bid;
use common\models\EmployeeRejectedBid;
use common\models\Push;
use common\repository\BidRepository;
use common\repository\EmployeeRejectedBidRepository;
use common\service\BidService;
use paragraph1\phpFCM\Notification;
use scl\yii\push\job\PushUserJob;
use Throwable;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\di\NotInstantiableException;
use yii\queue\JobInterface;
use yii\queue\Queue;

/**
 * Class BidFindEmployeeJob
 * @package common\jobs
 */
class BidFindEmployeeJob extends BaseObject implements JobInterface
{
    /** @var integer $bidId */
    public $bidId;

    /**
     * @param Queue $queue
     * @return void|null
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function execute($queue)
    {
        /** @var BidRepository $bidRepository */
        $bidRepository = Yii::$container->get(BidRepository::class);
        /** @var EmployeeRejectedBidRepository $employeeRejectedBidRepository */
        $employeeRejectedBidRepository = Yii::$container->get(EmployeeRejectedBidRepository::class);
        /** @var BidService $bidService */
        $bidService = Yii::$container->get(BidService::class);

        $bid = $bidRepository->get($this->bidId);
        if ($bid === null) {
            return;
        }

        if ($bid->status !== Bid::STATUS_NEW) {
            return;
        }

        $employee = $bidService->getFirstAvailableEmployee($bid->id, [$bid->employee_id ?? 0]);
        if (!$employee) {
            $bid->employee_id = null;
            $bid->status = Bid::STATUS_CANCELED;
            $bidRepository->update($bid, false);
            $bid->trigger(Bid::EVENT_BID_CANCELED);
            return;
        }

        if ($bid->employee_id) {
            $employeeRejectedBid = EmployeeRejectedBid::create($bid->employee_id, $bid->id);
            $employeeRejectedBidRepository->insert($employeeRejectedBid);
        }

        $bid->employee_id = $employee->id;
        if (!$bidRepository->update($bid)) {
            return;
        }

        $notificationEmployee = new Notification(
            Yii::t('app', 'An bid has been assigned to you.'),
            Yii::t('app', 'An bid has been assigned to you.')
        );

        Yii::$app->queue->push(
            new PushUserJob(
                [$bid->employee_id],
                Push::TYPE_EMPLOYEE,
                $notificationEmployee
            ));

        $bid->trigger(Bid::EVENT_CREATE_BID_BY_CUSTOMER);
    }
}
