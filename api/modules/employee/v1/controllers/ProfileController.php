<?php

namespace api\modules\employee\v1\controllers;

use api\misc\UploadedFileBase64;
use api\modules\employee\v1\request\ProfileAddFcmTokenRequest;
use api\modules\employee\v1\request\ProfileEditRequest;
use api\modules\employee\v1\request\ProfileImageUploadRequest;
use api\modules\employee\v1\request\ProfileViewRequest;
use common\service\EmployeeService;
use common\services\UserService;
use scl\tools\rest\exceptions\SafeException;
use scl\yii\tools\controllers\RestController;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ProfileController
 * @package api\modules\employee\v1\controllers
 */
class ProfileController extends RestController
{
    /**
     * @var EmployeeService
     */
    private $employeeService;

    /**
     * ProfileController constructor.
     * @param $id
     * @param $module
     * @param EmployeeService $employeeService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        EmployeeService $employeeService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->employeeService = $employeeService;
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
     *     tags={"profile employee"},
     *     path="/employee/v1/profile",
     *     summary="getting information of employee profile",
     *     description="login to server by code and token",
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/ProfileResponse"),
     *     ),
     *     @OA\Response(response="404", description="Employee not found"),
     * )
     *
     * @return ProfileViewRequest|\api\response\ProfileResponse
     * @throws SafeException|NotFoundHttpException
     */
    public function actionView()
    {
        $employeeId = Yii::$app->user->getId();
        /** @var ProfileViewRequest $request */
        $request = new ProfileViewRequest(['employeeId' => $employeeId]);
        if (!$request->validate()) {
            return $request;
        }

        $profileDto = UserService::getProfile($employeeId);
        if ($profileDto === null) {
            throw new SafeException(404, Yii::t('app', 'Employee not found'));
        }
        return new \api\response\ProfileResponse($profileDto);

//        $profileDto = $this->employeeService->getProfile($request->getEmployeeId());
//        if ($profileDto === null) {
//            throw new SafeException(404, Yii::t('app', 'Employee not found'));
//        }
//
//        return new ProfileResponse($profileDto);
    }

    /**
     * @OA\Put(
     *     tags={"profile employee"},
     *     path="/employee/v1/profile",
     *     summary="update information of employee profile",
     *     description="update information of employee profile",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmployeeProfileEditRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/ProfileResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="500", description="Update failed"),
     *     @OA\Response(response="404", description="Employee not found"),
     * )
     *
     * @return ProfileEditRequest|\api\response\ProfileResponse
     * @throws SafeException
     * @throws \Throwable
     */
    public function actionUpdate()
    {
        $employeeId = Yii::$app->user->getId();
        /** @var ProfileEditRequest $request */
        $request = new ProfileEditRequest(Yii::$app->request->post());
        if (!$request->validate()) {
            return $request;
        }

        try {
            $profileDto = UserService::updateProfile($employeeId, $request->attributes);
            //$profileDto = $this->employeeService->updateProfile($employeeId, $request);
        } catch (\Exception $exception) {
            throw new SafeException(500, Yii::t('app', 'Update failed'));
        }
        if ($profileDto === null) {
            throw new SafeException(404, Yii::t('app', 'Employee not found'));
        }

        return new \api\response\ProfileResponse($profileDto);
        //return new ProfileResponse($profileDto);
    }

    /**
     * @OA\Post(
     *     tags={"profile employee"},
     *     path="/employee/v1/profile/photo",
     *     summary="upload employee profile photo",
     *     description="upload employee profile photo",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmployeeProfileImageUploadRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/EmployeeProfileResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="400", description="Bad request"),
     *     @OA\Response(response="404", description="Employee not found"),
     * )
     *
     * @return ProfileImageUploadRequest|\api\response\ProfileResponse
     * @throws SafeException
     * @throws BadRequestHttpException|NotFoundHttpException
     */
    public function actionUploadImage()
    {
        /** @var ProfileImageUploadRequest $request */
        $request = new ProfileImageUploadRequest(['photo' => UploadedFileBase64::getInstanceByName('photo')]);
        if (!$request->validate()) {
            return $request;
        }

        $profileDto = UserService::uploadImage($request->photo);
        return new \api\response\ProfileResponse($profileDto);

//        $profileDto = $this->employeeService->uploadImage($request);
//        return new ProfileRespons($profileDto);
    }

    /**
     * @OA\Post(
     *     tags={"profile employee"},
     *     path="/employee/v1/profile/fcm-token",
     *     summary="add employee fcm token",
     *     description="add employee fcm token",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/EmployeeProfileAddFcmTokenRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/ProfileResponse"),
     *     ),
     *     @OA\Response(response="500", description="Update tokens list failed"),
     * )
     *
     * @return ProfileAddFcmTokenRequest|\api\response\ProfileResponse
     * @throws SafeException|NotFoundHttpException
     */
    public function actionAddFcmToken()
    {
        $request = new ProfileAddFcmTokenRequest(Yii::$app->request->post());
        if (!$request->validate()) {
            return $request;
        }

        $employeeId = Yii::$app->user->getId();

        if ($profileDto = UserService::addFcmToken($employeeId, $request->getToken())) {
            return new \api\response\ProfileResponse($profileDto);
        } else {
            throw new SafeException(500, Yii::t('app', 'Update tokens list failed'));
        }
    }


    /**
     * @OA\Delete(
     *     tags={"profile employee"},
     *     path="/employee/v1/profile/fcm-token/{token}",
     *     summary="remove employee fcm token",
     *     description="remove employee fcm token",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/ProfileResponse"),
     *     ),
     *     @OA\Response(response="500", description="Update tokens list failed"),
     * )
     *
     * @param string $token
     * @return ProfileAddFcmTokenRequest|\api\response\ProfileResponse
     * @throws SafeException|NotFoundHttpException
     */
    public function actionRemoveFcmToken(string $token)
    {
        $request = new ProfileAddFcmTokenRequest(['token' => $token]);
        if (!$request->validate()) {
            return $request;
        }

        $employeeId = Yii::$app->user->getId();

        if ($profileDto = UserService::removeFcmToken($employeeId, $request->getToken())) {
            return new \api\response\ProfileResponse($profileDto);
        } else {
            throw new SafeException(500, Yii::t('app', 'Update tokens list failed'));
        }
    }


    /**
     * @OA\Delete(
     *     tags={"profile employee"},
     *     path="/employee/v1/profile/remove-all-fcm-tokens",
     *     summary="remove all employee fcm tokens",
     *     description="remove all employee fcm tokens",
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/ProfileResponse"),
     *     ),
     *     @OA\Response(response="500", description="Update tokens list failed"),
     * )
     *
     * @return \api\response\ProfileResponse
     * @throws SafeException|NotFoundHttpException
     */
    public function actionRemoveAllFcmTokens()
    {
        $employeeId = Yii::$app->user->getId();

        if ($profileDto = UserService::removeAllFcmTokens($employeeId)) {
            return new \api\response\ProfileResponse($profileDto);
        } else {
            throw new SafeException(500, Yii::t('app', 'Update tokens list failed'));
        }
    }
}
