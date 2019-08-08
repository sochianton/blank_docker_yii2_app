<?php

namespace api\modules\employee\v1\controllers;

use api\modules\employee\v1\request\BidApplyRequest;
use api\modules\employee\v1\request\BidDoneRequest;
use api\modules\employee\v1\request\BidSearchRequest;
use api\modules\employee\v1\response\BidListResponse;
use api\modules\employee\v1\response\BidResponse;
use common\service\BidService;
use scl\yii\tools\controllers\RestController;
use Yii;
use yii\base\Module;
use yii\db\StaleObjectException;
use yii\filters\auth\HttpBearerAuth;
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
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeBidListResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Employee not found"),
     * )
     *
     * @return BidListResponse
     */
    public function actionIndex()
    {
        $isArchive = Yii::$app->request->get('archive', 'false');
        $isArchive = ($isArchive === 'true') ? true : false;

        $userId = Yii::$app->user->getId();
        $bids = $this->bidService->getListEmployee($userId, $isArchive);

        return new BidListResponse($bids);
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
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeBidResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Customer not found"),
     * )
     * @param int $bidId
     * @return BidResponse
     * @throws NotFoundHttpException
     */
    public function actionView(int $bidId)
    {
        $userId = Yii::$app->user->getId();
        $bidDto = $this->bidService->getEmployee($bidId, $userId);

        return new BidResponse($bidDto);
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
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeBidListResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Employee not found"),
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
        $bids = $this->bidService->searchEmployee($userId, $request->getTerm(), $request->getStatus());

        return new BidListResponse($bids);
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
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeBidResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @param int $bidId
     * @return BidApplyRequest|BidResponse
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionApply(int $bidId)
    {
        $request = new BidApplyRequest(Yii::$app->request->bodyParams);
        if (!$request->validate()) {
            return $request;
        }

        $userId = Yii::$app->user->getId();
        $bidDto = $this->bidService->apply($bidId, $userId, $request->isApply());

        return new BidResponse($bidDto);
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
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeBidResponse"),
     *     ),
     *     @OA\Response(response="404", description="Bid not found"),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @param int $bidId
     * @return BidDoneRequest|BidResponse
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
        $bidDto = $this->bidService->done($bidId, $userId, $request->getComment(), $request->getPhotos());

        return new BidResponse($bidDto);
    }
}
