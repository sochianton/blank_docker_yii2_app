<?php

namespace common\service;

use common\dto\BidDto;
use common\helpers\UploadFileHelper;
use common\models\Bid;
use common\models\BidAttachment;
use common\models\Employee;
use common\models\EmployeeRejectedBid;
use common\models\Push;
use common\repository\BidRepository;
use common\repository\BidWorkRepository;
use common\repository\CustomerRepository;
use common\repository\EmployeeQualificationRepository;
use common\repository\EmployeeRejectedBidRepository;
use common\repository\EmployeeRepository;
use common\repository\QualificationRepository;
use common\repository\WorkQualificationRepository;
use common\repository\WorkRepository;
use paragraph1\phpFCM\Notification;
use scl\tools\rest\exceptions\SafeException;
use scl\yii\push\job\PushUserJob;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UploadedFile;

/**
 * Class BidService
 * @package common\service
 */
class BidService
{
    /**
     * @var BidRepository
     */
    private $bidRepository;
    /**
     * @var BidWorkRepository
     */
    private $bidWorkRepository;
    /**
     * @var WorkRepository
     */
    private $workRepository;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var EmployeeRepository
     */
    private $employeeRepository;
    /**
     * @var EmployeeRejectedBidRepository
     */
    private $employeeRejectedBidRepository;
    /**
     * @var WorkQualificationRepository
     */
    private $workQualificationRepository;
    /**
     * @var EmployeeQualificationRepository
     */
    private $employeeQualificationRepository;
    /**
     * @var QualificationRepository
     */
    private $qualificationRepository;

    /**
     * BidService constructor.
     * @param BidRepository $bidRepository
     * @param BidWorkRepository $bidWorkRepository
     * @param WorkRepository $workRepository
     * @param CustomerRepository $customerRepository
     * @param EmployeeRepository $employeeRepository
     * @param EmployeeRejectedBidRepository $employeeRejectedBidRepository
     * @param WorkQualificationRepository $workQualificationRepository
     * @param EmployeeQualificationRepository $employeeQualificationRepository
     * @param QualificationRepository $qualificationRepository
     */
    public function __construct(
        BidRepository $bidRepository,
        BidWorkRepository $bidWorkRepository,
        WorkRepository $workRepository,
        CustomerRepository $customerRepository,
        EmployeeRepository $employeeRepository,
        EmployeeRejectedBidRepository $employeeRejectedBidRepository,
        WorkQualificationRepository $workQualificationRepository,
        EmployeeQualificationRepository $employeeQualificationRepository,
        QualificationRepository $qualificationRepository
    ) {
        $this->bidRepository = $bidRepository;
        $this->bidWorkRepository = $bidWorkRepository;
        $this->workRepository = $workRepository;
        $this->customerRepository = $customerRepository;
        $this->employeeRepository = $employeeRepository;
        $this->employeeRejectedBidRepository = $employeeRejectedBidRepository;
        $this->workQualificationRepository = $workQualificationRepository;
        $this->employeeQualificationRepository = $employeeQualificationRepository;
        $this->qualificationRepository = $qualificationRepository;
    }

    /**
     * @param int $id
     * @return Bid
     * @throws NotFoundHttpException
     */
    public function get(int $id): Bid
    {
        $bid = $this->bidRepository->get($id);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        return $bid;
    }

    /**
     * @param $name
     * @throws NotFoundHttpException
     */
    public function deleteFile(string $name)
    {
        $path = UploadFileHelper::getFilePath($name);
        if (!$path) {
            throw new NotFoundHttpException(Yii::t('errors', 'File not found'));
        }

        if ($this->bidRepository->deleteFile($name)) {
            UploadFileHelper::deleteFile($path);
        }
    }

    /**
     * @param int $id
     * @param int $type
     * @return array
     */
    public function getFiles(int $id, int $type): array
    {
        $photos = $this->bidRepository->getFiles($id, $type);
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
    public function getWorkIds(int $id): array
    {
        return $this->bidWorkRepository->getWorkIds($id);
    }

    /**
     * @param int $id
     * @param int $userId
     * @return BidDto|null
     * @throws NotFoundHttpException
     */
    public function getCustomer(int $id, int $userId): ?BidDto
    {
        $bid = $this->bidRepository->getCustomer($id, $userId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        $works = $this->workRepository->getWorksByBidId($bid->id);
        $photos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $files = $this->getFiles($bid->id, BidAttachment::TYPE_FILE);
        $employeePhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $qualificationIds = $this->workQualificationRepository->getQualificationIds($works[0]->id ?? 0);
        $qualification = $this->qualificationRepository->get($qualificationIds[0] ?? 0);

        return $this->getDto($bid, $works, $photos, $files, $employeePhotos, $qualification->name ?? '');
    }

    /**
     * @param int $customerId
     * @param bool $isArchive
     * @return BidDto[]
     */
    public function getListCustomer(int $customerId, bool $isArchive = false): array
    {
        $list = $this->bidRepository->getListCustomer($customerId, $isArchive);
        return array_map(function (Bid $model) {
            $works = $this->workRepository->getWorksByBidId($model->id);
            $qualificationIds = $this->workQualificationRepository->getQualificationIds($works[0]->id ?? 0);
            $qualification = $this->qualificationRepository->get($qualificationIds[0] ?? 0);
            return $this->getDto($model, $works, [], [], [], $qualification->name ?? '');
        }, $list);
    }

    /**
     * @param int $customerId
     * @param string $term
     * @param int|null $status
     * @return array
     */
    public function searchCustomer(int $customerId, string $term, ?int $status = null): array
    {
        $list = $this->bidRepository->searchCustomer($customerId, $term, $status);
        return array_map(function (Bid $model) {
            $works = $this->workRepository->getWorksByBidId($model->id);
            return $this->getDto($model, $works);
        }, $list);
    }

    /**
     * @param int $id
     * @param int $userId
     * @return BidDto|null
     * @throws NotFoundHttpException
     */
    public function getEmployee(int $id, int $userId): ?BidDto
    {
        $bid = $this->bidRepository->getEmployee($id, $userId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        $works = $this->workRepository->getWorksByBidId($bid->id);
        $customerPhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $files = $this->getFiles($bid->id, BidAttachment::TYPE_FILE);
        $employeePhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $qualificationIds = $this->workQualificationRepository->getQualificationIds($works[0]->id ?? 0);
        $qualification = $this->qualificationRepository->get($qualificationIds[0] ?? 0);

        return $this->getDto($bid, $works, $customerPhotos, $files, $employeePhotos, $qualification->name ?? '');
    }

    /**
     * @param int $employeeId
     * @param bool $isArchive
     * @return BidDto[]
     */
    public function getListEmployee(int $employeeId, bool $isArchive = false): array
    {
        $list = $this->bidRepository->getListEmployee($employeeId, $isArchive);
        return array_map(function (Bid $model) {
            $works = $this->workRepository->getWorksByBidId($model->id);
            $qualificationIds = $this->workQualificationRepository->getQualificationIds($works[0]->id ?? 0);
            $qualification = $this->qualificationRepository->get($qualificationIds[0] ?? 0);

            return $this->getDto($model, $works, [], [], [], $qualification->name ?? '');
        }, $list);
    }

    /**
     * @param int $employeeId
     * @param string $term
     * @param int|null $status
     * @return array
     */
    public function searchEmployee(int $employeeId, string $term, ?int $status = null): array
    {
        $list = $this->bidRepository->searchEmployee($employeeId, $term, $status);
        return array_map(function (Bid $model) {
            $works = $this->workRepository->getWorksByBidId($model->id);
            return $this->getDto($model, $works);
        }, $list);
    }

    /**
     * @param BidDto $bidDto
     * @return BidDto
     * @throws ForbiddenHttpException
     * @throws SafeException
     * @throws ServerErrorHttpException
     * @throws Throwable
     */
    public function create_old(BidDto $bidDto): BidDto
    {
        if ($this->customerRepository->isBlocked($bidDto->getCustomerId())) {
            throw new ForbiddenHttpException(Yii::t('app', 'You not have permissions to create bid.'));
        }

        $workIds = $bidDto->getWorks();
        $employee = $this->getFirstAvailableEmployeeByWorks($workIds);

        if ($employee === null) {
            $notification = new Notification(
                Yii::t('app', 'No matching employee found.'),
                Yii::t('app', 'No matching employee found for your bid.')
            );

            Yii::$app->queue->push(
                new PushUserJob(
                    [$bidDto->getCustomerId()],
                    Push::TYPE_CUSTOMER,
                    $notification
                ));

            throw new BadRequestHttpException(Yii::t('app', 'No matching employee found for your bid.'));
        }

        $bid = Bid::create(
            $bidDto->getName(),
            $bidDto->getCustomerId(),
            $employee->id,
            $bidDto->getStatus(),
            $bidDto->getPrice(),
            $bidDto->getCompleteAt(),
            $bidDto->getObject(),
            $bidDto->getCustomerComment(),
            $bidDto->getEmployeeComment()
        );

        $bid = $this->bidRepository->insert($bid);
        if ($bid === null) {
            throw new ServerErrorHttpException(Yii::t('errors', 'Can\'t create bid for unknown reason.'));
        }

        if (!empty($workIds)) {
            $this->bidWorkRepository->insertAll($bid->id, $workIds);
        }

        $notificationEmployee = new Notification(
            Yii::t('app', 'An bid has been assigned to you.'),
            Yii::t('app', 'An bid has been assigned to you.')
        );

        Yii::$app->queue->push(
            new PushUserJob(
                [$bid->employee_id],
                Push::TYPE_EMPLOYEE,
                $notificationEmployee
            ));

        $bid->trigger(Bid::EVENT_CREATE_BID_BY_CUSTOMER);

        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_CUSTOMER, $bidDto->getCustomerPhotos());
        $this->uploadFiles($bid, BidAttachment::TYPE_FILE, $bidDto->getFiles());
        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $bidDto->getEmployeePhotos());


        $customerPhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $employeePhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $files = $this->getFiles($bid->id, BidAttachment::TYPE_FILE);

        $works = $this->workRepository->getWorksByBidId($bid->id);
        return $this->getDto($bid, $works, $customerPhotos, $files, $employeePhotos);
    }

    /**
     * @param BidDto $bidDto
     * @return BidDto
     * @throws ForbiddenHttpException
     * @throws SafeException
     * @throws ServerErrorHttpException
     * @throws Throwable
     */
    public function create(BidDto $bidDto): BidDto
    {

        if ($this->customerRepository->isBlocked($bidDto->getCustomerId())) {
            throw new ForbiddenHttpException(Yii::t('app', 'You not have permissions to create bid.'));
        }

        $workIds = $bidDto->getWorks();
        $employees = $this->getAllAvailableEmployeeByWorks($workIds);

        if ($employees === null OR empty($employees)) {
            $notification = new Notification(
                Yii::t('app', 'No matching employee found.'),
                Yii::t('app', 'No matching employee found for your bid.')
            );

            Yii::$app->queue->push(
                new PushUserJob(
                    [$bidDto->getCustomerId()],
                    Push::TYPE_CUSTOMER,
                    $notification
                ));

            throw new BadRequestHttpException(Yii::t('app', 'No matching employee found for your bid.'));
        }

        $bid = Bid::create(
            $bidDto->getName(),
            $bidDto->getCustomerId(),
            null,
            $bidDto->getStatus(),
            $bidDto->getPrice(),
            $bidDto->getCompleteAt(),
            $bidDto->getObject(),
            $bidDto->getCustomerComment(),
            $bidDto->getEmployeeComment()
        );

        $bid = $this->bidRepository->insert($bid);
        if ($bid === null) {
            throw new ServerErrorHttpException(Yii::t('errors', 'Can\'t create bid for unknown reason.'));
        }

        if (!empty($workIds)) {
            $this->bidWorkRepository->insertAll($bid->id, $workIds);
        }

        $notificationEmployee = new Notification(
            Yii::t('app', 'An bid has been assigned to you.'),
            Yii::t('app', 'An bid has been assigned to you.')
        );



        Yii::$app->queue->push(
            new PushUserJob(
                ArrayHelper::getColumn($employees, 'id'),
                Push::TYPE_EMPLOYEE,
                $notificationEmployee
            )
        );

        $bid->trigger(Bid::EVENT_CREATE_BID_BY_CUSTOMER);

        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_CUSTOMER, $bidDto->getCustomerPhotos());
        $this->uploadFiles($bid, BidAttachment::TYPE_FILE, $bidDto->getFiles());
        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $bidDto->getEmployeePhotos());

        $customerPhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $employeePhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $files = $this->getFiles($bid->id, BidAttachment::TYPE_FILE);

        $works = $this->workRepository->getWorksByBidId($bid->id);
        return $this->getDto($bid, $works, $customerPhotos, $files, $employeePhotos);
    }

    /**
     * @param int $id
     * @param BidDto $bidDto
     * @return BidDto
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function update(int $id, BidDto $bidDto): BidDto
    {
        /** @var Bid $bid */
        $bid = $this->bidRepository->get($id);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Can\'t find bid.'));
        }

        $bid->name = $bidDto->getName();
        $bid->customer_id = $bidDto->getCustomerId();
        $bid->employee_id = $bidDto->getEmployeeId();
        $bid->status = $bidDto->getStatus();
        $bid->price = $bidDto->getPrice();
        $bid->object = $bidDto->getObject();
        $bid->complete_at = $bidDto->getCompleteAt();
        $bid->customer_comment = $bidDto->getCustomerComment();
        $bid->employee_comment = $bidDto->getEmployeeComment();

        $bid = $this->bidRepository->update($bid);
        if ($bid === null) {
            throw new ServerErrorHttpException(Yii::t('app', 'Can\'t update bid.'));
        }

        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_CUSTOMER, $bidDto->getCustomerPhotos());
        $this->uploadFiles($bid, BidAttachment::TYPE_FILE, $bidDto->getFiles());
        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $bidDto->getEmployeePhotos());

        $this->bidWorkRepository->deleteAll($id);

        $workIds = $bidDto->getWorks();

        if (!empty($workIds)) {
            $this->bidWorkRepository->insertAll($bid->id, $workIds);
        }

        $works = $this->workRepository->getWorksByBidId($bid->id);

        return $this->getDto($bid, $works);
    }

    /**
     * Customer can cancel bid before work start.
     * @param int $id
     * @param int $customerId
     * @return BidDto
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function cancel(int $id, int $customerId): BidDto
    {
        $bid = $this->bidRepository->getCustomer($id, $customerId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        if ($bid->status !== Bid::STATUS_NEW) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t change status'));
        }

        $bid = $this->bidRepository->setStatus($bid, Bid::STATUS_CANCELED);

        $works = $this->workRepository->getWorksByBidId($bid->id);

        return $this->getDto($bid, $works);
    }

    /**
     * Customer can approve bid (or send to arbitration) when work complete.
     * @param int $id
     * @param int $customerId
     * @param bool $approve
     * @return BidDto
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function approve(int $id, int $customerId, bool $approve): BidDto
    {
        $bid = $this->bidRepository->getCustomer($id, $customerId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        if ($bid->status !== Bid::STATUS_CONFIRMATION) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t approve work, incompatible status.'));
        }

        $works = $this->workRepository->getWorksByBidId($bid->id);

        if ($approve) {
            $bid = $this->bidRepository->setStatus($bid, Bid::STATUS_COMPLETE);
            $employee = $this->employeeRepository->get($bid->employee_id);
            if ($employee === null) {
                throw new NotFoundHttpException(Yii::t('app', 'Employee not found'));
            }
            $work = array_shift($works);
            $this->employeeRepository->transferFundsToBalance($employee, $bid->price, $work->commission);
        } else {
            $bid = $this->bidRepository->setStatus($bid, Bid::STATUS_ARBITRATION);
        }

        return $this->getDto($bid, $works);
    }

    /**
     * Employee can take bid to work or reject.
     * @param int $id
     * @param int $employeeId
     * @param bool $apply
     * @return BidDto
     *
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     * @throws UnprocessableEntityHttpException
     */
    public function apply(int $id, int $employeeId, bool $apply): BidDto
    {
        // Пользователь заблокирован
        if ($this->employeeRepository->isBlocked($employeeId)) {
            throw new ForbiddenHttpException(Yii::t('app', 'You not have permissions to apply bid.'));
        }

        // Находим Заявку по ID
        $bid = $this->bidRepository->get($id);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        // Только НОВЫЕ заявки
        if ($bid->status !== Bid::STATUS_NEW) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t apply work, incompatible status.'));
        }

        // Только НОВЫЕ заявки
        if (!$bid->employee_id) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Work already has employee'));
        }

        if($apply){

            $bid = $this->bidRepository->setStatus($bid, Bid::STATUS_IN_WORK);
            $bid->employee_id = $employeeId;
            $bid = $this->bidRepository->update($bid);

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

            $this->employeeRejectedBidRepository->deleteAll($bid->id);
            $bid->trigger(Bid::EVENT_APPLY_BID_BY_EMPLOYEE);
        }
        elseif(false){
            $rejectEmployeeId = $bid->employee_id;

            if (!empty($rejectEmployeeId)) {
                $employeeRejectedBid = EmployeeRejectedBid::create($rejectEmployeeId, $bid->id);
                $this->employeeRejectedBidRepository->insert($employeeRejectedBid);
            }

            $employee = $this->getFirstAvailableEmployee($bid->id);

            if ($employee != null) {
                $bid->employee_id = $employee->id;
                $this->bidRepository->update($bid);

                $notificationEmployee = new Notification(
                    Yii::t('app', 'An bid has been assigned to you.'),
                    Yii::t('app', 'An bid has been assigned to you.')
                );

                Yii::$app->queue->push(
                    new PushUserJob(
                        [$bid->employee_id],
                        Push::TYPE_EMPLOYEE,
                        $notificationEmployee
                    ));

                $bid->trigger(Bid::EVENT_REJECT_BID_BY_EMPLOYEE);
            } else {
                $bid->status = Bid::STATUS_CANCELED;
                $bid->employee_id = null;
                $this->bidRepository->update($bid);
            }
        }

        $works = $this->workRepository->getWorksByBidId($bid->id);

        $customerPhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $employeePhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $files = $this->getFiles($bid->id, BidAttachment::TYPE_FILE);

        return $this->getDto($bid, $works, $customerPhotos, $files, $employeePhotos);
    }

    /**
     * Employee can mark work of bid completed.
     * @param int $id
     * @param int $employeeId
     * @param null|string $comment
     * @param array $photos
     * @return BidDto
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     * @throws UnprocessableEntityHttpException
     */
    public function done(int $id, int $employeeId, ?string $comment, array $photos): BidDto
    {
        $bid = $this->bidRepository->getEmployee($id, $employeeId);
        if ($bid === null) {
            throw new NotFoundHttpException(Yii::t('errors', 'Can\'t find bid.'));
        }

        if ($bid->status !== Bid::STATUS_IN_WORK) {
            throw new UnprocessableEntityHttpException(Yii::t('errors', 'Can\'t apply work, incompatible status.'));
        }

        $bid->status = Bid::STATUS_CONFIRMATION;
        $bid->employee_comment = $comment;
        $this->bidRepository->update($bid);

        $this->uploadFiles($bid, BidAttachment::TYPE_PHOTO_EMPLOYEE, $photos);

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

        $works = $this->workRepository->getWorksByBidId($bid->id);

        $customerPhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_CUSTOMER);
        $employeePhotos = $this->getFiles($bid->id, BidAttachment::TYPE_PHOTO_EMPLOYEE);
        $files = $this->getFiles($bid->id, BidAttachment::TYPE_FILE);

        return $this->getDto($bid, $works, $customerPhotos, $files, $employeePhotos);
    }

    /**
     * @param int $id
     * @return Bid|null
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function block(int $id): ?Bid
    {
        $bid = $this->get($id);
        $bid->deleted_at = time();
        $bid = $this->bidRepository->update($bid);

        return $bid;
    }

    /**
     * @param int $id
     * @return Bid|null
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function restore(int $id): ?Bid
    {
        $bid = $this->get($id);
        $bid->deleted_at = null;
        $bid = $this->bidRepository->update($bid);

        return $bid;
    }

    /**
     * @param int $bidId
     * @param array $excludeIds
     * @return Employee|null
     */
    public function getFirstAvailableEmployee(int $bidId, array $excludeIds = []): ?Employee
    {
        $workIds = $this->bidWorkRepository->getWorkIds($bidId);
        if (empty($workIds)) {
            return null;
        }

        $excludedEmployeeIds = $this->employeeRejectedBidRepository->getEmployeeIdsByBidId($bidId);
        $excludedEmployeeIds = array_unique(array_filter(array_merge($excludedEmployeeIds, $excludeIds)));

        return $this->getFirstAvailableEmployeeByWorks($workIds, $excludedEmployeeIds);
    }

    /**
     * @param array $workIds
     * @param array $excludedEmployeeIds
     * @return Employee[]|null
     */
    public function getFirstAvailableEmployeeByWorks(array $workIds, array $excludedEmployeeIds = []): ?Employee
    {
        $workId = array_shift($workIds);
        $workQualificationIds = $this->workQualificationRepository->getQualificationIds($workId);
        $includedEmployeeIds = $this->employeeQualificationRepository->getEmployeeIdsByQualifications($workQualificationIds);

        return $this->employeeRepository->getAllAvailable($includedEmployeeIds, $excludedEmployeeIds);
    }

    /**
     * @param array $workIds
     * @param array $excludedEmployeeIds
     * @return Employee[]|null
     */
    public function getAllAvailableEmployeeByWorks(array $workIds, array $excludedEmployeeIds = []): ?array
    {
        $workId = array_shift($workIds);
        $workQualificationIds = $this->workQualificationRepository->getQualificationIds($workId);
        $includedEmployeeIds = $this->employeeQualificationRepository->getEmployeeIdsByQualifications($workQualificationIds);

        return $this->employeeRepository->getAllAvailable($includedEmployeeIds, $excludedEmployeeIds);
    }

    /**
     * @param Bid $bid
     * @param int $type
     * @param array $newFiles
     * @throws SafeException
     */
    private function uploadFiles(Bid $bid, int $type, array $newFiles = []): void
    {
        $count = $this->bidRepository->getFilesCount($bid->id, $type);

        if (($count + count($newFiles)) > $this->getMaxFiles($type)) {
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

        $this->bidRepository->saveFiles($files);
    }

    /**
     * @param int $type
     * @return int
     */
    private function getMaxFiles(int $type): int
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
     * @param Bid $model
     * @param array $works
     * @param array $customerPhotos
     * @param array $files
     * @param array $employeePhotos
     * @param string $qualificationName
     * @return BidDto
     */
    protected function getDto(
        Bid $model,
        array $works,
        array $customerPhotos = [],
        array $files = [],
        array $employeePhotos = [],
        string $qualificationName = ''
    ): BidDto {
        return new BidDto(
            (int)$model->id,
            (string)$model->name,
            (int)$model->customer_id,
            (int)$model->employee_id,
            (int)$model->status,
            (int)$model->price,
            (string)$model->object,
            $model->customer_comment,
            $model->employee_comment,
            $model->complete_at,
            $model->created_at,
            $model->updated_at,
            (array)$works,
            (array)$customerPhotos,
            (array)$files,
            (array)$employeePhotos,
            (string)$qualificationName
        );
    }
}
