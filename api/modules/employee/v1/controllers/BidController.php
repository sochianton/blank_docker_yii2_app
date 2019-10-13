<?php

namespace api\modules\employee\v1\controllers;

use api\modules\employee\v1\request\BidApplyRequest;
use api\modules\employee\v1\request\BidDoneRequest;
use api\modules\employee\v1\request\BidSearchRequest;
use api\modules\employee\v1\response\BidListResponse;
use api\modules\employee\v1\response\BidResponse;
use common\ar\Bid;
use common\service\BidService;
use scl\yii\tools\controllers\RestController;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\db\StaleObjectException;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class BidController
 * @package api\modules\employee\v1\controllers
 */
class BidController extends RestController
{
    /**
     * @var BidService
     */
    private $bidService;

    /**
     * BidController constructor.
     * @param string $id
     * @param Module $module
     * @param BidService $bidService
     * @param array $config
     */
    public function __construct(
        string $id,
        Module $module,
        BidService $bidService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->bidService = $bidService;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => HttpBearerAuth::class,
            ],
        ];
    }

    /**
     * @OA\Get(
     *     tags={"bid employee"},
     *     path="/employee/v1/bid",
     *     summary="getting list of employee bids",
     *     description="getting list of employee bids",
     *     @OA\Parameter(
     *         name="archive",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeBidListResponse2"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Employee not found"),
     * )
     *
     * @return \api\response\BidListResponse
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $isArchive = Yii::$app->request->get('archive', 'false');
        $isArchive = ($isArchive === 'true') ? true : false;

        $userId = Yii::$app->user->getId();
        $bids = \common\services\BidService::getListForEmployee($userId, $isArchive);

        return new \api\response\BidListResponse($bids);
    }

    /**
     * @OA\Get(
     *     tags={"bid employee"},
     *     path="/employee/v1/bid/{bidId}",
     *     summary="view customer bid",
     *     description="view customer bid",
     *     @OA\Parameter(
     *         name="bidId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/BidResponse2"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Customer not found"),
     * )
     * @param int $bidId
     * @return \api\response\BidResponse
     * @throws NotFoundHttpException
     */
    public function actionView(int $bidId)
    {
        $userId = Yii::$app->user->getId();
        $bid = \common\services\BidService::getForEmployee($bidId, $userId);

        return new \api\response\BidResponse(\common\services\BidService::getDto($bid));
    }

    /**
     * @OA\Get(
     *     tags={"bid employee"},
     *     path="/employee/v1/bid/search",
     *     summary="getting search results of employee bids",
     *     description="getting search results of employee bids",
     *     @OA\Parameter(
     *         name="term",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeBidListResponse2"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Employee not found"),
     * )
     *
     * @return BidSearchRequest|\api\response\BidListResponse
     * @throws InvalidConfigException
     */
    public function actionSearch()
    {
        $request = new BidSearchRequest(Yii::$app->request->queryParams);
        if (!$request->validate()) {
            return $request;
        }
        $userId = Yii::$app->user->getId();

        $bids = \common\services\BidService::searchForEmployee($userId, $request->getTerm(), $request->getStatus());
        return new \api\response\BidListResponse($bids);
    }

    /**
     * @OA\Put(
     *     tags={"bid employee"},
     *     path="/employee/v1/bid/apply/{bidId}",
     *     summary="apply employee bid",
     *     description="apply employee bid (if apply value `true` status changes to `IN_WORK` else emloyee disattached from bid)",
     *     @OA\Parameter(
     *         name="bidId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmployeeBidApplyRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/BidResponse2"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @param int $bidId
     * @return BidApplyRequest|\api\response\BidResponse
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionApply(int $bidId)
    {



//        $request = new BidApplyRequest(Yii::$app->request->bodyParams);
//        if (!$request->validate()) {
//            return $request;
//        }
//
//        die(print_r($request, true));
//
//        $userId = Yii::$app->user->getId();
//        $bid = \common\services\BidService::apply($bidId, $userId, $request->isApply());

        $userId = Yii::$app->user->getId();
        $bid = \common\services\BidService::apply($bidId, $userId, true);

        return new \api\response\BidResponse(\common\services\BidService::getDto($bid));
    }

    /**
     * @OA\Put(
     *     tags={"bid employee"},
     *     path="/employee/v1/bid/done/{bidId}",
     *     summary="done employee bid",
     *     description="done employee bid (Status changes to `CONFIRMATION`)",
     *     @OA\Parameter(
     *         name="bidId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmployeeBidDoneRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/BidResponse2"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @param int $bidId
     * @return BidDoneRequest|\api\response\BidResponse
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     */
    public function actionDone(int $bidId)
    {
        $request = new BidDoneRequest(Yii::$app->request->bodyParams);
        if (!$request->validate()) {
            return $request;
        }

        $userId = Yii::$app->user->getId();
        $bid = \common\services\BidService::done($bidId, $userId, $request->getComment(), $request->getPhotos());
        return new \api\response\BidResponse(\common\services\BidService::getDto($bid));

//        $bidDto = $this->bidService->done($bidId, $userId, $request->getComment(), $request->getPhotos());
//
//        return new BidResponse($bidDto);
    }

    /**
     * @OA\Get(
     *     tags={"bid employee"},
     *     path="/employee/v1/bid/search-all-open",
     *     summary="getting search results of all open bids",
     *     description="getting search results of all open bids",
     *
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/BidResponse2"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Employee not found"),
     * )
     *
     * @return \api\response\BidResponse[]
     * @throws \Throwable
     */
    public function actionSearchAllOpen(){

        $bid = new Bid();

//        $params = [$bid->formName() => Yii::$app->request->queryParams];
//        $params[$bid->formName()]['status'] = Bid::STATUS_NEW;

        $params = ArrayHelper::merge(Yii::$app->request->queryParams, [
            $bid->formName() => [
                'status' => Bid::STATUS_NEW,
            ],
            'employee_id' => false,
        ]);

//        die(print_r($params, true));

        return array_map(function($bidArr){
            return new \api\response\BidResponse($bidArr);
        }, \common\services\BidService::searchFromApi($params));

        //return \common\services\BidService::searchFromApi($params);

    }
}
