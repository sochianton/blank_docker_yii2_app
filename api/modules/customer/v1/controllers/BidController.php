<?php

namespace api\modules\customer\v1\controllers;

use api\modules\customer\v1\request\BidApproveRequest;
use api\modules\customer\v1\request\BidCreateRequest;
use api\modules\customer\v1\request\BidSearchRequest;
use api\modules\customer\v1\response\BidListResponse;
use api\modules\customer\v1\response\BidResponse;
use common\service\BidService;
use scl\yii\tools\controllers\RestController;
use OpenApi\Annotations as OA;
use Yii;
use yii\base\Module;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class BidController
 * @package api\modules\customer\v1\controllers
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
     *     tags={"bid customer"},
     *     path="/customer/v1/bid",
     *     summary="getting list of customer bids",
     *     description="getting list of customer bids",
     *     @OA\Parameter(
     *         name="archive",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerBidListResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed")
     * )
     *
     * @return BidListResponse
     */
    public function actionIndex()
    {
        $isArchive = Yii::$app->request->get('archive', 'false');
        $isArchive = ($isArchive === 'true') ? true : false;

        $userId = Yii::$app->user->getId();
        $bids = $this->bidService->getListCustomer($userId, $isArchive);

        return new BidListResponse($bids);
    }

    /**
     * @OA\Get(
     *     tags={"bid customer"},
     *     path="/customer/v1/bid/{bidId}",
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
     *          @OA\JsonContent(ref="#/components/schemas/CustomerBidResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     * @param int $bidId
     * @return BidResponse
     * @throws NotFoundHttpException
     */
    public function actionView(int $bidId)
    {
        $userId = Yii::$app->user->getId();
        $bidDto = $this->bidService->getCustomer($bidId, $userId);

        return new BidResponse($bidDto);
    }

    /**
     * @OA\Get(
     *     tags={"bid customer"},
     *     path="/customer/v1/bid/search",
     *     summary="getting search results of customer bids",
     *     description="getting search results of customer bids",
     *     @OA\Parameter(
     *         name="term",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerBidListResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @return BidSearchRequest|BidListResponse
     */
    public function actionSearch()
    {
        $request = new BidSearchRequest(Yii::$app->request->queryParams);
        if (!$request->validate()) {
            return $request;
        }
        $userId = Yii::$app->user->getId();
        $bids = $this->bidService->searchCustomer($userId, $request->getTerm(), $request->getStatus());

        return new BidListResponse($bids);
    }

    /**
     * @OA\Post(
     *     tags={"bid customer"},
     *     path="/customer/v1/bid",
     *     summary="create customer bid",
     *     description="create customer bid",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CustomerBidCreateRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerBidResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="500", description="Create failed"),
     * )
     *
     * @return BidCreateRequest|BidResponse
     * @throws \Throwable
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $request = new BidCreateRequest(Yii::$app->request->post());
        if (!$request->validate()) {
            return $request;
        }

        $bidDto = $this->bidService->create($request->getDto());

        return new BidResponse($bidDto);
    }

    /**
     * @OA\Delete(
     *     tags={"bid customer"},
     *     path="/customer/v1/bid/{bidId}",
     *     summary="reject customer bid",
     *     description="reject customer bid",
     *     @OA\Parameter(
     *         name="bidId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerBidResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @param int $bidId
     * @return BidResponse
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function actionCancel(int $bidId)
    {
        $userId = Yii::$app->user->getId();
        $bidDto = $this->bidService->cancel($bidId, $userId);

        return new BidResponse($bidDto);
    }

    /**
     * @OA\Put(
     *     tags={"bid customer"},
     *     path="/customer/v1/bid/approve/{bidId}",
     *     summary="approve customer bid",
     *     description="approve customer bid (if approve value `true` status changes to `COMPLETE` else to `ARBITRATION`)",
     *     @OA\Parameter(
     *         name="bidId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CustomerBidApproveRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerBidResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @param int $bidId
     * @return BidApproveRequest|BidResponse
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function actionApprove(int $bidId)
    {
        $request = new BidApproveRequest(Yii::$app->request->post());
        if (!$request->validate()) {
            return $request;
        }

        $userId = Yii::$app->user->getId();
        $bidDto = $this->bidService->approve($bidId, $userId, $request->isApprove());

        return new BidResponse($bidDto);
    }
}
