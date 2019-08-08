<?php

namespace api\modules\customer\v1\controllers;

use api\misc\UploadedFileBase64;
use api\modules\customer\v1\request\ProfileAddFcmTokenRequest;
use api\modules\customer\v1\request\ProfileEditRequest;
use api\modules\customer\v1\request\ProfileImageUploadRequest;
use api\modules\customer\v1\request\ProfileViewRequest;
use api\modules\customer\v1\response\ProfileResponse;
use api\modules\customer\v1\response\TransactionListResponse;
use common\service\CustomerService;
use common\service\TransactionService;
use scl\tools\rest\exceptions\SafeException;
use scl\yii\tools\controllers\RestController;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\BadRequestHttpException;

/**
 * Class ProfileController
 * @package api\modules\customer\v1\controllers
 */
class ProfileController extends RestController
{
    /**
     * @var CustomerService
     */
    private $customerService;
    /**
     * @var TransactionService
     */
    private $transactionService;

    /**
     * ProfileController constructor.
     * @param $id
     * @param $module
     * @param CustomerService $customerService
     * @param TransactionService $transactionService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        CustomerService $customerService,
        TransactionService $transactionService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->customerService = $customerService;
        $this->transactionService = $transactionService;
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
     *     tags={"profile customer"},
     *     path="/customer/v1/profile",
     *     summary="getting information of customer profile",
     *     description="getting information of customer profile",
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerProfileResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="404", description="Customer not found"),
     * )
     *
     * @return ProfileViewRequest|ProfileResponse
     * @throws SafeException
     */
    public function actionView()
    {
        $customerId = Yii::$app->user->getId();
        /** @var ProfileViewRequest $request */
        $request = new ProfileViewRequest(['customerId' => $customerId]);
        if (!$request->validate()) {
            return $request;
        }

        $profileDto = $this->customerService->getProfile($request->getCustomerId());
        if ($profileDto === null) {
            throw new SafeException(404, Yii::t('app', 'Customer not found'));
        }

        return new ProfileResponse($profileDto);
    }

    /**
     * @OA\Put(
     *     tags={"profile customer"},
     *     path="/customer/v1/profile",
     *     summary="update information of customer profile",
     *     description="update information of customer profile",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CustomerProfileEditRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerProfileResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="500", description="Update failed"),
     *     @OA\Response(response="404", description="Customer not found"),
     * )
     *
     * @return ProfileEditRequest|ProfileResponse
     * @throws SafeException
     * @throws \Throwable
     */
    public function actionUpdate()
    {
        $customerId = Yii::$app->user->getId();
        /** @var ProfileEditRequest $request */
        $request = new ProfileEditRequest(Yii::$app->request->post());
        if (!$request->validate()) {
            return $request;
        }

        try {
            $profileDto = $this->customerService->updateProfile($customerId, $request);
        } catch (\Exception $exception) {
            throw new SafeException(500, Yii::t('app', 'Update failed'));
        }
        if ($profileDto === null) {
            throw new SafeException(404, Yii::t('app', 'Customer not found'));
        }

        return new ProfileResponse($profileDto);
    }

    /**
     * @OA\Post(
     *     tags={"profile customer"},
     *     path="/customer/v1/profile/photo",
     *     summary="upload customer profile photo",
     *     description="upload customer profile photo",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CustomerProfileImageUploadRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerProfileResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     *     @OA\Response(response="400", description="Bad request"),
     *     @OA\Response(response="404", description="Customer not found"),
     * )
     *
     * @return ProfileImageUploadRequest|ProfileResponse
     * @throws SafeException
     * @throws BadRequestHttpException
     */
    public function actionUploadImage()
    {
        /** @var ProfileImageUploadRequest $request */
        $request = new ProfileImageUploadRequest(['photo' => UploadedFileBase64::getInstanceByName('photo')]);
        if (!$request->validate()) {
            return $request;
        }

        $profileDto = $this->customerService->uploadImage($request);

        return new ProfileResponse($profileDto);
    }

    /**
     * @OA\Get(
     *     tags={"profile customer"},
     *     path="/customer/v1/profile/transactions",
     *     summary="getting information of customer transactions",
     *     description="getting information of customer transactions",
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerTransactionListResponse"),
     *     ),
     *     @OA\Response(response="422", description="Validation failed"),
     * )
     *
     * @return TransactionListResponse
     */
    public function actionTransactions()
    {
        $customerId = Yii::$app->user->getId();
        $startDate = time() - TransactionService::MONTH_UNIX;
        $endDate = time();

        $transactions = $this->transactionService->getList($startDate, $endDate, $customerId);

        return new TransactionListResponse($transactions);
    }

    /**
     * @OA\Post(
     *     tags={"profile customer"},
     *     path="/customer/v1/profile/fcm-token",
     *     summary="add customer fcm token",
     *     description="add customer fcm token",
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CustomerProfileAddFcmTokenRequest")),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerProfileResponse"),
     *     ),
     *     @OA\Response(response="500", description="Update tokens list failed"),
     * )
     *
     * @return ProfileAddFcmTokenRequest|ProfileResponse
     * @throws SafeException
     */
    public function actionAddFcmToken()
    {
        $request = new ProfileAddFcmTokenRequest(Yii::$app->request->post());
        if (!$request->validate()) {
            return $request;
        }

        $customerId = Yii::$app->user->getId();

        if ($profileDto = $this->customerService->addFcmToken($customerId, $request->getToken())) {
            return new ProfileResponse($profileDto);
        } else {
            throw new SafeException(500, Yii::t('app', 'Update tokens list failed'));
        }
    }


    /**
     * @OA\Delete(
     *     tags={"profile customer"},
     *     path="/customer/v1/profile/fcm-token/{token}",
     *     summary="remove customer fcm token",
     *     description="remove customer fcm token",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerProfileResponse"),
     *     ),
     *     @OA\Response(response="500", description="Update tokens list failed"),
     * )
     *
     * @param string $token
     * @return ProfileAddFcmTokenRequest|ProfileResponse
     * @throws SafeException
     */
    public function actionRemoveFcmToken(string $token)
    {
        $request = new ProfileAddFcmTokenRequest(['token' => $token]);
        if (!$request->validate()) {
            return $request;
        }

        $customerId = Yii::$app->user->getId();

        if ($profileDto = $this->customerService->removeFcmToken($customerId, $request->getToken())) {
            return new ProfileResponse($profileDto);
        } else {
            throw new SafeException(500, Yii::t('app', 'Update tokens list failed'));
        }
    }

    /**
     * @OA\Delete(
     *     tags={"profile customer"},
     *     path="/customer/v1/profile/remove-all-fcm-tokens",
     *     summary="remove all customer fcm tokens",
     *     description="remove all customer fcm tokens",
     *     @OA\Response(
     *          response="200",
     *          description="ok",
     *          @OA\JsonContent(ref="#/components/schemas/CustomerProfileResponse"),
     *     ),
     *     @OA\Response(response="500", description="Update tokens list failed"),
     * )
     *
     * @return ProfileResponse
     * @throws SafeException
     */
    public function actionRemoveAllFcmTokens()
    {
        $customerId = Yii::$app->user->getId();

        if ($profileDto = $this->customerService->removeAllFcmTokens($customerId)) {
            return new ProfileResponse($profileDto);
        } else {
            throw new SafeException(500, Yii::t('app', 'Update tokens list failed'));
        }
    }
}
