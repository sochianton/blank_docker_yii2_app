<?php

namespace common\jobs;

use common\ar\Bid;
use common\ar\EuroserviceIntegrate;
use common\components\CURL;
use common\repositories\BidRep;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\queue\JobInterface;

/**
 * Class BidEuroServiceIntergrateJob
 * @package common\jobs
 */
class BidEuroServiceIntergrateJob extends BaseObject implements JobInterface
{
    /** @var integer $bidId */
    public $bidId;



    protected $login = 'ias_api_user';
    protected $pasword = 'ias_test';

    protected $service_id = '75';
    const URL_REQUEST = 'https://sd.e-servis.ru/api/requests/';




    public function execute($queue)
    {

        /** @var Bid $bid */
        $bid = BidRep::get($this->bidId);


        /** @var EuroserviceIntegrate $link */
        $link = EuroserviceIntegrate::findOne([
            'bid_id' => $bid->id
        ]);

        if($link){
            $res = CURL::authPut(self::URL_REQUEST.$link->rid, Json::encode($this->getDefaultParams($bid)), $this->login, $this->pasword);
        }
        else{
            $res = CURL::authPost(self::URL_REQUEST, Json::encode($this->getDefaultParams($bid)), $this->login, $this->pasword);
            try{
                $res = Json::decode($res);
                if(isset($res['id'])){
                    $link = new EuroserviceIntegrate([
                        'bid_id' => $bid->id,
                        'rid' => (int)$res['id'],
                    ]);
                    $link->save();
                }
            }
            catch (\Exception $e){

            }

        }




    }






    protected function getDefaultParams(Bid $bid){

        $endDate = new \DateTime($bid->complete_at);
        if(!$endDate) $endDate = new \DateTime();

        $phone = $bid->customer_rl->phone?:'';

         return [
             'service_id' => $this->service_id,
             'CUsers_id' => $this->login,

             'City' => 'Москва',
             'Name' => $bid->name,
             'phone' => $phone,
             'Content' => $bid->customer_comment,
             'Address' => $bid->object,
             'Status' => $this->getStatus($bid->status),

             //'StartTime' => '11.09.2019 08:00',
             'EndTime' => $endDate->format('d.m.Y H:i'),

             'fields' => [
                'Стоимость' => $bid->price,
                'Комментарий НСК' => $bid->employee_comment,
             ]
         ];
    }

    protected function getStatus(int $status){

        switch ($status){
            case Bid::STATUS_CANCELED:
                return 'Отменена';

            case Bid::STATUS_IN_WORK:
                return 'В работе';

            case Bid::STATUS_CONFIRMATION:
                return 'Принята в исполнение';

            case Bid::STATUS_COMPLETE:
                return 'Завершена';

            case Bid::OP_UPDATE:
                return 'Просрочено исполнение';
        }

        return 'Открыта';
    }
}
