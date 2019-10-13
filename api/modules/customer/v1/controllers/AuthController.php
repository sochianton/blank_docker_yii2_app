<?php

namespace api\modules\customer\v1\controllers;

use api\modules\customer\v1\request\LoginRequest;
use api\modules\customer\v1\response\LoginResponse;
use api\modules\customer\v1\service\AuthService;
use OpenApi\Annotations as OA;
use scl\tools\rest\exceptions\SafeException;
use scl\yii\tools\controllers\RestController;
use yii\base\Exception;
use yii\base\Module;
use yii\filters\AccessControl;

/**
 * Class AuthController
 * @package api\modules\customer\v1\controllers
 */
class AuthController extends RestController
{
    /** @var AuthService $authService */
    private $authService;

    /**
     * AuthController constructor.
     * @param string $id
     * @param Module $module
     * @param AuthService $authService
     * @param array $config
     */
    public function __construct(
        string $id,
        Module $module,
        AuthService $authService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->authService = $authService;
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['login', 'logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['logout'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @OA\Post(
     *     tags={"auth customer"},
     *     path="/customer/v1/login",
     *     summary="login to server by code and token",
     *     description="login to server by code and token",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CustomerLoginRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerLoginResponse"),
     *     ),
     *     @OA\Response(response="422", description="Invalid confirmation code"),
     *     @OA\Response(response="500", description="Can`t create authentication token for unknown reason"),
     * )
     *
     * @return LoginRequest|LoginResponse
     * @throws \Throwable
     * @throws SafeException
     * @throws Exception
     */
    public function actionLogin()
    {
        $request = new LoginRequest($this->input);

        //\Yii::info('<pre>'.print_r($this->input, true).'</pre>');

        if (!$request->validate()) {
            return $request;
        }

        $token = $this->authService->login($request->getDto());

        return new LoginResponse($token);
    }
}
