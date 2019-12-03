<?php


namespace common\services;


use common\ar\Bid;
use common\ar\User;
use common\helpers\UploadFileHelper;
use common\interfaces\BaseServiceInterface;
use common\ar\BidAttachment;
use common\ar\Push;
use common\repositories\BidRep;
use common\repositories\UserRep;
use common\repositories\WorkRep;
use common\traits\ServiceTrait;
use paragraph1\phpFCM\Notification;
use scl\tools\rest\exceptions\SafeException;
use scl\yii\push\job\PushUserJob;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UploadedFile;

class BidService implements BaseServiceInterface
{

    use ServiceTrait;

    /** @var  BidRep*/
    static $repository = BidRep::class;

    static function getDto(Bid $bid): array{
        return $bid->toArray([
            'id',
            'name',
            'status',
            'price',
            'object',
        ], [

            'customerId',
            'employeeId',
            'completeAt',
            'createdAt',
            'updatedAt',
            'customerComment',
            'categoryName',
            'employeeComment',
            'works',
            'customerPhotos',
            'employeePhotos',
            'files',
        ]);
    }

    /**
     * @param int $id
     * @param int $type
     * @return array
     */
    static function getFiles(int $id, int $type): array
    {
        $photos = (self::$repository)::getFiles($id, $type);
        return array_map(function (BidAttachment $bidAttachment) use ($type) {
            return [
                'url' => $bidAttachment->getFileUrl($type),
                'path' => $bidAttachment->name,
                'name' => $bidAttachment->original_name,
            ];
        }, $photos);
    }

    /**
     * @param int $id
     * @return array
     */
    static function getWorkIds(int $id): array
    {
        return (self::$repository)::getWorkIds($id);
    }

    /**
     * @param $params
     * @return array
     * @throws \Exception
     */
    static function searchFromApi($params){

        $bid = new Bid();
        $provider = $bid->search($params);

        return array_map(function (Bid $model){
            return self::getDto($model);
        }, $provider->getModels());

    }

    /**
     * @param int $employeeId
     * @param $isArchive
     * @return array
     * @throws \yii\base\InvalidConfigException|\Exception
     */
    static function getListForEmployee(int $employeeId, $isArchive){

        $bid = new Bid();

        $params=[];
        $params[$bid->formName()] = [
            'employee_id' => (int)$employeeId,
            'status' => $isArchive?Bid::STATUSES_ARCHIVE:Bid::STATUSES_ACTIVE,
        ];

        return self::searchFromApi($params);
    }

    /**
     * @param int $customerId
     * @param $isArchive
     * @return array
     * @throws \yii\base\InvalidConfigException|\Exception
     */
    static function getListForCustomer(int $customerId, $isArchive){

        $bid = new Bid();

        $params=[];
        $params[$bid->formName()] = [
            'customer_id' => (int)$customerId,
            'status' => $isArchive?Bid::STATUSES_ARCHIVE:Bid::STATUSES_ACTIVE,
        ];

        return self::searchFromApi($params);
    }

    /**
     * @param int $employeeId
     * @param string $term
     * @param int|null $status
     * @return array
     * @throws \yii\base\InvalidConfigException|\Exception
     */
    static function searchForEmployee(int $employeeId, string $term, ?int $status = null){
        $bid = new Bid();

        $params=[];
        $params[$bid->formName()] = [
            'employee_id' => (int)$employeeId,
            'name' => $term,
            'status' => $status,
        ];



        return self::searchFromApi($params);
    }

    /**
     * @param int $employeeId
     * @param string $term
     * @param int|null $status
     * @return array
     * @throws \yii\base\InvalidConfigException|\Exception
     */
    static function searchForCustomer(int $customerId, string $term, ?int $status = null){
        $bid = new Bid();

        $params=[];
        $params[$bid->formName()] = [
            'customer_id' => (int)$customerId,
            'name' => $term,
            'status' => $status,
        ];

        return self::searchFromApi($params);
    }

    /**
     * @param Bid $bid
     * @return Bid
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     * @throws SafeException
     * @throws \Throwable|ServerErrorHttpException
     */
    static function insert($bid, bool $runValidation = true, $attributeNames = null): Bid
    {

        if(UserRep::isBlocked($bid->customer_id)){
            throw new ForbiddenHttpException(Yii::t('app', 'You not have permissions to create bid.'));
        }

        $employees = self::getAllAvailableEmployeeByWorks($bid->works);

        if ($employees === null OR empty($employees)) {
            $notification = new Notification(
                Yii::t('app', 'No matching employee found.'),
                Yii::t('app', 'No matching employee found for your bid.')
            );

            Yii::$app->queue->push(
                new PushUserJob(
                    [$bid->customer_id],
                    Push::TYPE_CUSTOMER,
                    $notification
                )
            );

            throw new BadRequestHttpException(Yii::t('app', 'No matching employee found for your bid.'));
        }

        /** @var Bid $bid */
        $bid = (self::$repository)::insert($bid);

        if ($bid === null) {
            throw new ServerErrorHttpException(Yii::t('errors', 'Can\'t create bid for unknown reason.'));
        }

        if (!empty($bid->works)) {
            WorkRep::insertAllByBid($bid->id, $bid->works);
        }

//        $notificationEmployee = new Notification(
//            Yii::t('app', 'An bid has been assigned to you.'),
//            Yii::t('app', 'An bid has been assigned to you.')
//        );

        $notificationEmployee = new Notification(
            Yii::t('app', 'New bid'),
            Yii::t('app', 'New bid has been created, suits for you')
        );

        Yii::$app->queue->push(
            new PushUserJob(
                ArrayHelper::getColumn($employees, 'id'),
                Push::TYPE_EMPLOYEE,
                $notificationEmployee
            )
        );

        $bid->trigger(Bid::EVENT_CREATE_BID_BY_CUSTOMER);


        if(is_array($bid->customerPhotos)) self::uploadFiles($bid, BidAttachment::TYPE_PHOTO_CUSTOMER, $bid->customerPhotos);
        if(is_array($bid->files))  self::uploadFiles($bid, BidAttachment::TYPE_FILE, $bid->files);
        if(is_array($bid->employeePhotos))  self::uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $bid->employeePhotos);


        $bid->trigger(Bid::EVENT_CREATE_UPDATE_BID);
        return (self::$repository)::get($bid->id);

//        $this->uploadFiles($bid, BidAttachment::TYPE_FILE, $bidDto->getFiles());
//        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $bidDto->getEmployeePhotos());
//        $customerPhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
//        $employeePhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
//        $files = $this->getFiles($bid->id, BidAttachment::TYPE_FILE);
//
//        $works = $this->workRepository->getWorksByBidId($bid->id);
//        return $this->getDto($bid, $works, $customerPhotos, $files, $employeePhotos);
    }

    /**
     * @param Bid $bid
     * @param bool $runValidation
     * @param null $attributeNames
     * @return Bid
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws SafeException
     * @throws ServerErrorHttpException
     * @throws \Throwable
     */
    static function update($bid, bool $runValidation = true, $attributeNames = null): Bid
    {
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Can\'t find bid.'));
        }

        if(!$bid->validate()){
            throw new ServerErrorHttpException(Json::encode($bid->errors));
        }

        /** @var Bid $bid */
        if ((self::$repository)::update($bid) === null) {
            throw new ServerErrorHttpException(Yii::t('errors', 'Can\'t update bid.'));
        }

        if(is_array($bid->customerPhotos)) self::uploadFiles($bid, BidAttachment::TYPE_PHOTO_CUSTOMER, $bid->customerPhotos);
        if(is_array($bid->files))  self::uploadFiles($bid, BidAttachment::TYPE_FILE, $bid->files);
        if(is_array($bid->employeePhotos))  self::uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $bid->employeePhotos);

//        self::uploadFiles($bid, BidAttachment::TYPE_PHOTO_CUSTOMER, $bid->customerPhotos);
//        self::uploadFiles($bid, BidAttachment::TYPE_FILE, $bid->files);
//        self::uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $bid->employeePhotos);

        WorkRep::deleteAllByBid($bid->id);

        if (!empty($bid->works)) {
            WorkRep::insertAllByBid($bid->id, $bid->works);
        }

        $bid->trigger(Bid::EVENT_CREATE_UPDATE_BID);
        return (self::$repository)::get($bid->id);
    }

    /**
     * @param int $id
     * @param int $employeeId
     * @param bool $apply
     * @return Bid
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     */
    static function apply(int $id, int $employeeId, bool $apply): Bid
    {
        // Пользователь заблокирован
        if(UserRep::isBlocked($employeeId)){
            throw new ForbiddenHttpException(Yii::t('app', 'You not have permissions to apply bid.'));
        }

        // Находим Заявку по ID
        /** @var Bid $bid */
        $bid = BidRep::getNew($id);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

//        die(print_r($bid->attributes, true));

        // Только НОВЫЕ заявки
        if ($bid->status !== Bid::STATUS_NEW) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t apply work, incompatible status.'));
        }

        // Только не назначенные заявки
        if ($bid->employee_id) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Work already has employee'));
        }

        if($apply){

            $bid = BidRep::setStatus($bid, Bid::STATUS_IN_WORK);

            $bid->employee_id = $employeeId;
            self::update($bid);

            $notificationEmployee = new Notification(
                Yii::t('app', 'An bid has been assigned to you.'),
                Yii::t('app', 'An bid has been assigned to you.')
            );

            Yii::$app->queue->push(
                new PushUserJob(
                    [$bid->employee_id],
                    Push::TYPE_EMPLOYEE,
                    $notificationEmployee
                )
            );

            BidRep::deleteAllEmployeeRejects($bid->id);
            $bid->trigger(Bid::EVENT_APPLY_BID_BY_EMPLOYEE);
        }

        return $bid;
    }

    /**
     * Customer can approve bid (or send to arbitration) when work complete.
     * @param int $id
     * @param int $customerId
     * @param bool $approve
     * @return Bid
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    static function approve(int $id, int $customerId, bool $approve): Bid
    {
        $bid = $bid = BidRep::getForCustomer($id, $customerId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        if ($bid->status !== Bid::STATUS_CONFIRMATION) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t approve work, incompatible status.'));
        }

        $works = WorkRep::getWorksByBidId($bid->id);

        if ($approve) {
            $bid = BidRep::setStatus($bid, Bid::STATUS_COMPLETE);
            $employee = UserRep::getByType($bid->employee_id, User::TYPE_EMPLOYEE);
            //$employee = $this->employeeRepository->get($bid->employee_id);
            if ($employee === null) {
                throw new NotFoundHttpException(Yii::t('app', 'Employee not found'));
            }
            $work = array_shift($works);
            UserService::transferFundsToBalance($employee, $bid->price, $work->commission);
            //$this->employeeRepository->transferFundsToBalance($employee, $bid->price, $work->commission);
        } else {
            $bid = BidRep::setStatus($bid, Bid::STATUS_ARBITRATION);
        }

        return $bid;
    }

    /**
     * @param int $id
     * @param int $employeeId
     * @param string|null $comment
     * @param array $photos
     * @return Bid
     * @throws NotFoundHttpException
     * @throws SafeException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     */
    static function done(int $id, int $employeeId, ?string $comment, array $photos): Bid
    {
        $bid = BidRep::getForEmployee($id, $employeeId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        if ($bid->status !== Bid::STATUS_IN_WORK) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t apply work, incompatible status.'));
        }

        $bid->status = Bid::STATUS_CONFIRMATION;
        $bid->employee_comment = $comment;
        $bid->employeePhotos = $photos;
        $bid = self::update($bid);

        $notification = new Notification(
            Yii::t('app', 'Work on your bid completed.'),
            Yii::t('app', 'Work on your bid completed.')
        );

        Yii::$app->queue->push(
            new PushUserJob(
                [$bid->customer_id],
                Push::TYPE_CUSTOMER,
                $notification
            ));

        $bid->trigger(Bid::EVENT_DONE_BID_BY_EMPLOYEE);
        return $bid;
    }

    /**
     * @param int $id
     * @param int $customerId
     * @return Bid
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    static function cancel(int $id, int $customerId): Bid
    {
        $bid = BidRep::getForCustomer($id, $customerId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        if ($bid->status !== Bid::STATUS_NEW) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t change status'));
        }

        $bid = BidRep::setStatus($bid, Bid::STATUS_CANCELED);

        return $bid;
    }

    /**
     * @param int $id
     * @param int $userId
     * @return Bid|null
     * @throws NotFoundHttpException
     */
    static function getForEmployee(int $id, int $userId): ?Bid
    {
        $bid = BidRep::getForEmployee($id, $userId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }
        return $bid;
    }

    /**
     * @param int $id
     * @param int $userId
     * @return Bid|null
     * @throws NotFoundHttpException
     */
    static function getForCustomer(int $id, int $userId): ?Bid
    {
        $bid = BidRep::getForCustomer($id, $userId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }
        return $bid;
    }

    /**
     * @param Bid $bid
     * @param int $type
     * @param array $newFiles
     * @throws \scl\tools\rest\exceptions\SafeException|\Exception
     */
    static function uploadFiles(Bid $bid, int $type, array $newFiles = []): void
    {
        $count = BidRep::getFilesCount($bid->id, $type);

        if (($count + count($newFiles)) > self::getMaxFiles($type)) {
            throw new SafeException(422, Yii::t('app', 'Max files count'));
        }

        $files = [];

        /** @var UploadedFile $newFile */
        foreach ($newFiles as $newFile) {
            $extension = UploadFileHelper::getExtensionByMime($newFile->type, false);
            $fileName = UploadFileHelper::generateFileName($extension);

            if (!empty($newFile->content)) {
                UploadFileHelper::createFileFromBase64($fileName, $newFile->content);
            } else {
                $filePath = UploadFileHelper::getFilePath($fileName);
                if (!move_uploaded_file($newFile->tempName, $filePath)) {
                    continue;
                }
            }

            $files[] = [
                'bid_id' => $bid->id,
                'type' => $type,
                'name' => $fileName,
                'original_name' => $newFile->name
            ];
        }

        BidRep::saveFiles($files);
    }

    /**
     * @param int $type
     * @return int
     */
    static function getMaxFiles(int $type): int
    {
        switch ($type) {
            case BidAttachment::TYPE_PHOTO_CUSTOMER:
                return BidAttachment::MAX_PHOTOS_CUSTOMER;
            case BidAttachment::TYPE_PHOTO_EMPLOYEE:
                return BidAttachment::MAX_PHOTOS_EMPLOYEE;
            case BidAttachment::TYPE_FILE:
            default:
                return BidAttachment::MAX_FILES;
        }
    }

    /**
     * @param array $workIds
     * @param array $excludedEmployeeIds
     * @return User[]|null
     */
    static function getAllAvailableEmployeeByWorks(array $workIds, array $excludedEmployeeIds = []): ?array
    {

//        $workId = array_shift($workIds);
//        $workQualificationIds = QualificationRep::getQualificationIdsByWork($workId);
//        $includedEmployeeIds = UserRep::getEmployeeIdsByQualifications($workQualificationIds);

        return UserRep::getAllByWork($workIds, $excludedEmployeeIds, User::TYPE_EMPLOYEE);
    }

}