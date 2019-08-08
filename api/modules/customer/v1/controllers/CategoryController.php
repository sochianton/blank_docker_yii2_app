<?php

namespace api\modules\customer\v1\controllers;

use api\modules\customer\v1\response\CategoryListResponse;
use common\service\QualificationService;
use OpenApi\Annotations as OA;
use scl\yii\tools\controllers\RestController;
use yii\base\Module;

/**
 * Class CategoryController
 * @package api\modules\customer\v1\controllers
 */
class CategoryController extends RestController
{
    /**
     * @var QualificationService
     */
    private $qualificationService;

    /**
     * AuthController constructor.
     * @param string $id
     * @param Module $module
     * @param QualificationService $qualificationService
     * @param array $config
     */
    public function __construct(
        string $id,
        Module $module,
        QualificationService $qualificationService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->qualificationService = $qualificationService;
    }


    /**
     * @OA\Get(
     *     tags={"category"},
     *     path="/customer/v1/category",
     *     summary="get list categories",
     *     description="get list categories",
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerCategoryListResponse"),
     *     )
     * )
     *
     * @return CategoryListResponse
     */
    public function actionIndex()
    {
        $categories = $this->qualificationService->getList();

        return new CategoryListResponse($categories);
    }
}