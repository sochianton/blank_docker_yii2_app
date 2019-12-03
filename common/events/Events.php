<?php

namespace common\events;

use common\ar\EuroserviceIntegrate;
use common\components\CURL;
use common\jobs\BidDoneNotApprovedJob;
use common\ar\Bid;
use common\jobs\BidEuroServiceIntergrateJob;
use common\models\Push;
use paragraph1\phpFCM\Notification;
use scl\yii\push\job\PushUserJob;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\queue\amqp_interop\Queue;

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
    const DELAY_CHECK_INTEGRATE = 10; // 5 minute

    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        Event::on(Bid::class, Bid::EVENT_CREATE_BID_BY_CUSTOMER, [$this, 'bidCreateCustomer']);
        Event::on(Bid::class, Bid::EVENT_APPLY_BID_BY_EMPLOYEE, [$this, 'bidApplyEmployee']);
        Event::on(Bid::class, Bid::EVENT_REJECT_BID_BY_EMPLOYEE, [$this, 'bidRejectEmployee']);
        Event::on(Bid::class, Bid::EVENT_DONE_BID_BY_EMPLOYEE, [$this, 'bidDoneEmployee']);
        Event::on(Bid::class, Bid::EVENT_BID_CANCELED, [$this, 'bidCanceled']);
        Event::on(Bid::class, Bid::EVENT_CREATE_UPDATE_BID, [$this, 'updateBidInEuroService']);
    }

    /**
     * @param Event $event
     * @throws InvalidConfigException
     */
    public function bidCreateCustomer(Event $event)
    {
        /** @var Bid $bid */
        $bid = $event->sender;

        // Поскольку заявка уходит всем, то искать по очередно не нужно

//        Yii::$app->queue->delay(self::DELAY_CHECK_REQUEST_BID)->push(Yii::createObject([
//            'class' => BidFindEmployeeJob::class,
//            'bidId' => $bid->id,
//        ]));
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

//        Yii::$app->queue->delay(self::DELAY_CHECK_REQUEST_BID)->push(Yii::createObject([
//            'class' => BidFindEmployeeJob::class,
//            'bidId' => $bid->id,
//        ]));
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


    /**
     * Интеграция с сервисом euroservice
     * @param Event $event
     */
    public function updateBidInEuroService(Event $event){

        /** @var Bid $bid */
        $bid = $event->sender;



//        $login = 'ias_api_user';
//        $password = 'ias_test';
//
//        $endDate = new \DateTime($bid->complete_at);
//        if(!$endDate) $endDate = new \DateTime();
//
//        $phone = $bid->customer_rl->phone?:'';
//
//        $data = [
//            'service_id' => '75',
//            'CUsers_id' => $login,
//
//            'City' => 'Москва',
//            'Name' => $bid->name,
//            'phone' => $phone,
//            'Content' => $bid->customer_comment,
//            'Address' => $bid->object,
//
//            //'StartTime' => '11.09.2019 08:00',
//            'EndTime' => $endDate->format('d.m.Y H:i'),
//
//            'fields' => [
//                'Стоимость' => $bid->price,
//                'Комментарий НСК' => $bid->employee_comment,
//            ]
//        ];
//
//        /** @var EuroserviceIntegrate $link */
//        $link = EuroserviceIntegrate::findOne([
//            'bid_id' => $bid->id
//        ]);
//
//        if($link){
//            $res = CURL::authPut('https://sd.e-servis.ru/api/requests/'.$link->rid, Json::encode($data), $login, $password);
//            die(print_r($res, true));
//        }
//        else{
//            die('sdf');
//        }



        /** @var Queue $queue */
        $queue = Yii::$app->queue;


        $queue->push(new BidEuroServiceIntergrateJob([
            'bidId' => $bid->id,
        ]));

    }
}
