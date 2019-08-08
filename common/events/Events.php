<?php

namespace common\events;

use common\jobs\BidDoneNotApprovedJob;
use common\jobs\BidFindEmployeeJob;
use common\models\Bid;
use common\models\Push;
use paragraph1\phpFCM\Notification;
use scl\yii\push\job\PushUserJob;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Class Events
 * @package common\events
 */
class Events implements BootstrapInterface
{
    // TODO: Need to return values back after test

    //  const DELAY_CHECK_REQUEST_BID = 60 * 30; // 30 minutes
    //  const DELAY_CHECK_DONE_BID = 60 * 60 * 24; // 24 hours
    const DELAY_CHECK_REQUEST_BID = 60 * 2; // 2 minute
    const DELAY_CHECK_DONE_BID = 60 * 5; // 5 minute

    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        Event::on(Bid::class, Bid::EVENT_BID_CREATE_CUSTOMER, [$this, 'bidCreateCustomer']);
        Event::on(Bid::class, Bid::EVENT_BID_APPLY_EMPLOYEE, [$this, 'bidApplyEmployee']);
        Event::on(Bid::class, Bid::EVENT_BID_REJECT_EMPLOYEE, [$this, 'bidRejectEmployee']);
        Event::on(Bid::class, Bid::EVENT_BID_DONE_EMPLOYEE, [$this, 'bidDoneEmployee']);
        Event::on(Bid::class, Bid::EVENT_BID_CANCELED, [$this, 'bidCanceled']);
    }

    /**
     * @param Event $event
     * @throws InvalidConfigException
     */
    public function bidCreateCustomer(Event $event)
    {
        /** @var Bid $bid */
        $bid = $event->sender;

        Yii::$app->queue->delay(self::DELAY_CHECK_REQUEST_BID)->push(Yii::createObject([
            'class' => BidFindEmployeeJob::class,
            'bidId' => $bid->id,
        ]));
    }

    public function bidApplyEmployee(Event $event)
    {
        /** @var Bid $bid */
        $bid = $event->sender;

        $notification = new Notification(
            Yii::t('app', 'Your bid is taken to work.'),
            Yii::t('app', 'Your bid is taken to work.')
        );

        Yii::$app->queue->push(
            new PushUserJob(
                [$bid->customer_id],
                Push::TYPE_CUSTOMER,
                $notification
            ));
    }

    /**
     * @param Event $event
     * @throws InvalidConfigException
     */
    public function bidRejectEmployee(Event $event)
    {
        /** @var Bid $bid */
        $bid = $event->sender;

        Yii::$app->queue->delay(self::DELAY_CHECK_REQUEST_BID)->push(Yii::createObject([
            'class' => BidFindEmployeeJob::class,
            'bidId' => $bid->id,
        ]));
    }

    /**
     * @param Event $event
     * @throws InvalidConfigException
     */
    public function bidDoneEmployee(Event $event)
    {
        /** @var Bid $bid */
        $bid = $event->sender;

        Yii::$app->queue->delay(self::DELAY_CHECK_DONE_BID)->push(Yii::createObject([
            'class' => BidDoneNotApprovedJob::class,
            'bidId' => $bid->id,
        ]));
    }

    /**
     * @param Event $event
     */
    public function bidCanceled(Event $event)
    {
        /** @var Bid $bid */
        $bid = $event->sender;

        $notification = new Notification(
            Yii::t('app', 'No matching employee found.'),
            Yii::t('app', 'No matching employee found for your bid.')
        );

        Yii::$app->queue->push(
            new PushUserJob(
                [$bid->customer_id],
                Push::TYPE_CUSTOMER,
                $notification
            ));
    }
}
