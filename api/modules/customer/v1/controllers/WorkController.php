<?php

namespace api\modules\customer\v1\controllers;

use api\modules\customer\v1\request\WorkListRequest;
use api\modules\customer\v1\response\WorkListResponse;
use common\dto\WorkDto;
use common\service\WorkService;
use scl\yii\tools\controllers\RestController;
use Yii;
use yii\filters\auth\HttpBearerAuth;

/**
 * Class WorkController
 * @package api\modules\customer\v1\controllers
 */
class WorkController extends RestController
{
    /** @var WorkService $workService */
    private $workService;

    /**
     * WorkController constructor.
     * @param $id
     * @param $module
     * @param WorkService $workService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        WorkService $workService,
        array $config = []
    ) {
        $this->workService = $workService;
        parent::__construct($id, $module, $config);
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
     *     tags={"work customer"},
     *     path="/customer/v1/work",
     *     summary="getting list of works",
     *     description="getting list of works",
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/WorkListResponse"),
     *     )
     * )
     *
     * @return WorkListRequest|WorkListResponse
     */
    public function actionIndex()
    {
        $request = new WorkListRequest(Yii::$app->request->queryParams);
        if (!$request->validate()) {
            return $request;
        }
        /** @var WorkDto[] $works */
        $works = $this->workService->getAll($request->getCategory());

        return new WorkListResponse($works);
    }
}
