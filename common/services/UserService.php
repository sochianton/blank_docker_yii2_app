<?php


namespace common\services;


use common\ar\AuthAssignment;
use common\ar\Push;
use common\ar\User;
use common\ar\Work;
use common\helpers\UploadFileHelper;
use common\interfaces\BaseServiceInterface;
use common\repositories\PushRep;
use common\repositories\UserRep;
use common\repositories\WorkRep;
use common\traits\ServiceTrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserService implements BaseServiceInterface
{

    use ServiceTrait;

    /** @var  UserRep*/
    static $repository = UserRep::class;

    /**
     * @param User $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return \common\ar\Work
     * @throws \Throwable
     */
    static function insert($model, bool $runValidation = true, $attributeNames = null) :User
    {

        if(!$model->isNewRecord){
            throw new NotFoundHttpException(Yii::t('app', 'Model is not new'));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {

            (self::$repository)::insert($model);

            self::deleteRolesAssignment([$model->id]);
            self::insertAssignmentRolesArray($model->id, $model->formRoles);

            (self::$repository)::deleteWorkArray([$model->id]);
            if (!empty($model->works)) {
                (self::$repository)::insertWorkArray($model->id, $model->works);
            }

//            $qualifications=$model->qualifications;
//            if($qualifications AND !is_array($qualifications)){
//                $qualifications = array($model->qualifications);
//            }
//
//            if (is_array($qualifications) AND !empty($qualifications)) {
//
//                (self::$repository)::insertQualificationArray($model->id, $qualifications);
//            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        if ($model->formPhoto !== null) {
            $model->saveImage();
        }

        return $model;
    }

    /**
     * @param User $model
     * @param bool $runValidation
     * @param null $attributeNames
     * @return Work
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    static function update($model, bool $runValidation = true, $attributeNames = null): User
    {

        if($model->isNewRecord){
            throw new NotFoundHttpException(Yii::t('app', 'Model need to be not new'));
        }

        (self::$repository)::update($model);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            (self::$repository)::update($model);

            self::deleteRolesAssignment([$model->id]);
            self::insertAssignmentRolesArray($model->id, $model->formRoles);

            (self::$repository)::deleteWorkArray([$model->id]);
            if (!empty($model->works)) {
                (self::$repository)::insertWorkArray($model->id, $model->works);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        if ($model->formPhoto !== null) {
            $model->saveImage();
        }

        return $model;
    }

    static function getDto(User $user): array{
        return $user->toArray([
           'email',
           'phone',
        ], [
            'balance',
            'name',
            'secondName',
            'lastName',
            'photo',
            'fcmTokens',
            'qualificationsAndWorks',
        ]);
    }

    /**
     * @param int $id
     * @param array|null $params
     * @return array|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    static function updateProfile(int $id, array $params=null): ?array
    {
        /** @var User $employee */
        $employee = (self::$repository)::get($id);
        if ($employee === null) {
            return null;
        }

        if (isset($params['email'])) $employee->email = $params['email'];
        if (isset($params['phone'])) $employee->phone = $params['phone'];
        if (isset($params['name'])) $employee->first_name = $params['name'];
        if (isset($params['lastName'])) $employee->last_name = $params['lastName'];
        if (isset($params['secondName'])) $employee->second_name = $params['secondName'];

        $employee = self::update($employee);
        if ($employee === null) {
            return null;
        }

        return self::getDto($employee);
    }

    /**
     * @param $photo
     * @return array|null
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \scl\tools\rest\exceptions\SafeException
     */
    static function uploadImage($photo): ?array
    {
        $employeeId = Yii::$app->user->getId();
        if ($photo === null) {
            throw new BadRequestHttpException(Yii::t('errors', 'File not found.'));
        }


        $employee = self::saveImage($employeeId, $photo);
        if ($employee === null) {
            return null;
        }

        return self::getDto($employee);
    }

    /**
     * @return array
     */
    static function getCurUserMenu(): array{

        if(Yii::$app->user->isGuest) return [
            [
                'label' => '<i class="fa fa-sign-in"></i>
                    <span>'.Yii::t('app', 'Login').'</span>',
                'url' => Url::toRoute(['/site/login']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'site' AND Yii::$app->controller->action->id == 'login')
                        return true;
                    return false;
                },
            ],
        ];

        return [
            [
                'label' => Yii::t('app', 'Main menu'),
                'options' => [
                    'class' => 'header'
                ],
            ],
            [
                'label' => '<i class="fa fa-users"></i>
                    <span>'.Yii::t('app', 'Users').'</span>',
                'url' => Url::toRoute(['/user']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'user' AND Yii::$app->controller->action->id != 'roles')
                        return true;
                    return false;
                },
                'visible' => Yii::$app->user->ch('/user/index')
            ],
            [
                'label' => '<i class="glyphicon glyphicon-equalizer"></i>
                    <span>'.Yii::t('app', 'Roles and permissions').'</span>',
                'url' => Url::toRoute(['/user/roles']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'user' AND Yii::$app->controller->action->id == 'roles')
                        return true;
                    return false;
                },
                'visible' => Yii::$app->user->ch('/user/roles')
            ],
            [
                'label' => '<i class="fa fa-industry"></i>
                    <span>'.Yii::t('app', 'Companies').'</span>',
                'url' => Url::toRoute(['/company']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'company')
                        return true;
                    return false;
                },
                'visible' => Yii::$app->user->ch('/company/index')
            ],
//            [
//                'label' => '<i class="fa fa-diamond"></i>
//                    <span>'.Yii::t('app', 'Customers').'</span>',
//                'url' => Url::toRoute(['/customer']),
//                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
//                    if(Yii::$app->controller->id == 'customer')
//                        return true;
//                    return false;
//                },
//            ],
//            [
//                'label' => '<i class="fa fa-life-ring"></i>
//                    <span>'.Yii::t('app', 'Employees').'</span>',
//                'url' => Url::toRoute(['/employee']),
//                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
//                    if(Yii::$app->controller->id == 'employee')
//                        return true;
//                    return false;
//                },
//            ],
            [
                'label' => '<i class="fa fa-folder-open"></i>
                    <span>'.Yii::t('app', 'Categories').'</span>',
                'url' => Url::toRoute(['/qualification']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'qualification')
                        return true;
                    return false;
                },
                'visible' => Yii::$app->user->ch('/qualification/index')
            ],
            [
                'label' => '<i class="fa fa-gavel"></i>
                    <span>'.Yii::t('app', 'Works').'</span>',
                'url' => Url::toRoute(['/work']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'work')
                        return true;
                    return false;
                },
                'visible' => Yii::$app->user->ch('/work/index')
            ],
            [
                'label' => '<i class="fa fa-ticket"></i>
                    <span>'.Yii::t('app', 'Bids').'</span>',
                'url' => Url::toRoute(['/bid']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'bid')
                        return true;
                    return false;
                },
                'visible' => Yii::$app->user->ch('/bid/index')
            ],
            [
                'label' => '<i class="fa fa-exchange"></i>
                    <span>'.Yii::t('app', 'Transactions').'</span>',
                'url' => Url::toRoute(['/transaction']),
                'active' => function ($item, $hasActiveChild, $isItemActive, $widget){
                    if(Yii::$app->controller->id == 'transaction')
                        return true;
                    return false;
                },
                'visible' => Yii::$app->user->ch('/transaction/index')
            ],
        ];
    }

    static function getCustomerList(): array
    {
        return ArrayHelper::map((self::$repository)::getAllCustomerList(), 'id', 'last_name');
    }

    static function getEmployeeList(): array
    {
        return ArrayHelper::map((self::$repository)::getAllEmployeeList(), 'id', 'last_name');
    }

    static function getQualificationIds(int $id) : array{
        return (self::$repository)::getQualificationIds($id);
    }

    /**
     * @param int $id user Id
     * @return array
     */
    static function getWorksIds(int $id) : array{
        return (self::$repository)::getWorksIds($id);
    }

    /**
     * @param int $id user Id
     * @return array
     */
    static function getCategoriedWorks(int $id) : array{
        return ArrayHelper::map(WorkRep::getAllWithCatsByIserId($id), 'id', 'name', 'c_name');
    }

    /**
     * @param int $id
     * @return array|null
     * @throws NotFoundHttpException
     */
    static function getProfile(int $id): ?array
    {
        /** @var User $employee */
        $employee = (self::$repository)::get($id);
        if ($employee === null) {
            return null;
        }

        return self::getDto($employee);
    }

    /**
     * @param int $userId
     * @return array
     */
    static function getFcmTokens(int $userId): array
    {
        return (new PushRep())->getPushTokenListById([$userId]);
    }

    /**
     * @param int $userId
     * @param string $token
     * @return array|null
     * @throws NotFoundHttpException
     */
    static function addFcmToken(int $userId, string $token): ?array
    {
        /** @var User $user */
        $user = (self::$repository)::get($userId);

        if (PushRep::add($userId, Push::TYPE_EMPLOYEE, $token)) {
            return self::getDto($user);
        }

        return null;
    }

    /**
     * @param int $userId
     * @param string $token
     * @return array|null
     * @throws NotFoundHttpException
     */
    static function removeFcmToken(int $userId, string $token): ?array
    {
        /** @var User $user */
        $user = (self::$repository)::get($userId);

        if ((new PushRep())->removeTokensById([$token])) {
            return self::getDto($user);
        }

        return null;
    }

    /**
     * @param int $userId
     * @return array|null
     * @throws NotFoundHttpException
     */
    static function removeAllFcmTokens(int $userId): ?array
    {

        /** @var User $user */
        $user = (self::$repository)::get($userId);

        if (PushRep::removeTokensByUserId($userId)) {
            return self::getDto($user);
        }

        return null;
    }

    /**
     * @param int $id
     * @param UploadedFile $photo
     * @return User|null
     * @throws NotFoundHttpException
     * @throws \scl\tools\rest\exceptions\SafeException
     */
    static function saveImage(int $id, UploadedFile $photo): ?User{

        /** @var User $user */
        $user = (self::$repository)::get($id);
        if ($user === null) {
            return null;
        }

        $oldFileName = $user->photo;
        $uploadedFile = $photo;


        $extension = UploadFileHelper::getExtensionByMime($uploadedFile->type, false);
        $fileName = UploadFileHelper::generateFileName($extension);

        if (!empty($uploadedFile->content)) {
            UploadFileHelper::createFileFromBase64($fileName, $uploadedFile->content);
        } else {
            $filePath = UploadFileHelper::getFilePath($fileName);
            if (!move_uploaded_file($uploadedFile->tempName, $filePath)) {
                return null;
            }
        }

        UserRep::updatePhoto($user, $fileName);

        if ($oldFileName) {
            $oldFilePath = UploadFileHelper::getFilePath($oldFileName);
            UploadFileHelper::deleteFile($oldFilePath);
        }

        return $user;


    }

    /**
     * @param User $employee
     * @param float $price
     * @param float $commission
     * @return User|null
     */
    static function transferFundsToBalance(User $employee, float $price, float $commission): ?User
    {
        $amount = $price - ($price * ($commission / 100));
        $balance = bcadd($employee->balance, $amount);
        $employee->updateAttributes(['balance' => $balance]);

        return $employee;
    }

    /**
     * @param int $user_id
     * @param array $roles
     * @throws \Exception
     */
    static function insertAssignmentRolesArray(int $user_id, array $roles): void
    {

        $transaction = Yii::$app->db->beginTransaction();
        try {

            $query = [];
            foreach ($roles as $role) {
                $query[] = [
                    'item_name' => $role,
                    'user_id' => $user_id,
                    'created_at' => time()
                ];
            }

            Yii::$app->db->createCommand()->batchInsert(
                AuthAssignment::tableName(),
                ['item_name', 'user_id', 'created_at'],
                $query
            )->execute();

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

    }

    /**
     * @param array $userIds
     * @return int
     */
    static function deleteRolesAssignment(array $userIds){

        return AuthAssignment::deleteAll(['user_id' => $userIds]);

    }



}