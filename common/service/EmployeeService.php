<?php

namespace common\service;

use api\modules\employee\v1\dto\ProfileDto;
use api\modules\employee\v1\request\ProfileEditRequest;
use api\modules\employee\v1\request\ProfileImageUploadRequest;
use common\dto\EmployeeDto;
use common\helpers\UploadFileHelper;
use common\models\Customer;
use common\models\Employee;
use common\models\Push;
use common\repository\BidRepository;
use common\repository\BidWorkRepository;
use common\repository\EmployeeQualificationRepository;
use common\repository\EmployeeRejectedBidRepository;
use common\repository\EmployeeRepository;
use common\repository\PushRepository;
use common\repository\WorkQualificationRepository;
use common\repository\WorkRepository;
use scl\tools\rest\exceptions\SafeException;
use Yii;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class EmployeeService
 *
 * @package common\service
 */
class EmployeeService
{
    /**
     * @var EmployeeRepository $employeeRepository
     */
    private $employeeRepository;
    /**
     * @var EmployeeQualificationRepository
     */
    private $employeeQualificationRepository;
    /**
     * @var EmployeeRejectedBidRepository
     */
    private $employeeRejectedBidRepository;
    /**
     * @var BidRepository
     */
    private $bidRepository;
    /**
     * @var WorkRepository
     */
    private $workRepository;
    /**
     * @var BidWorkRepository
     */
    private $bidWorkRepository;
    /**
     * @var WorkQualificationRepository
     */
    private $workQualificationRepository;
    /**
     * @var PushRepository $pushRepository
     */
    private $pushRepository;

    /**
     * EmployeeService constructor.
     * @param EmployeeRepository $employeeRepository
     * @param EmployeeQualificationRepository $employeeQualificationRepository
     * @param EmployeeRejectedBidRepository $employeeRejectedBidRepository
     * @param BidRepository $bidRepository
     * @param WorkRepository $workRepository
     * @param WorkQualificationRepository $workQualificationRepository
     * @param BidWorkRepository $bidWorkRepository
     * @param PushRepository $pushRepository
     */
    public function __construct(
        EmployeeRepository $employeeRepository,
        EmployeeQualificationRepository $employeeQualificationRepository,
        EmployeeRejectedBidRepository $employeeRejectedBidRepository,
        BidRepository $bidRepository,
        WorkRepository $workRepository,
        WorkQualificationRepository $workQualificationRepository,
        BidWorkRepository $bidWorkRepository,
        PushRepository $pushRepository
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->pushRepository = $pushRepository;
        $this->employeeQualificationRepository = $employeeQualificationRepository;
        $this->employeeRejectedBidRepository = $employeeRejectedBidRepository;
        $this->bidRepository = $bidRepository;
        $this->workRepository = $workRepository;
        $this->bidWorkRepository = $bidWorkRepository;
        $this->workQualificationRepository = $workQualificationRepository;
    }

    /**
     * @param int $id
     * @return Employee
     * @throws NotFoundHttpException
     */
    public function get(int $id): Employee
    {
        $employee = $this->employeeRepository->get($id);
        if ($employee === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Employee not found'));
        }

        return $employee;
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        $employees = $this->employeeRepository->getAllList();
        return ArrayHelper::map($employees, 'id', function ( $model) {
            return $model->getFullName();
        });
    }

    /**
     * @param int $id
     * @return array
     */
    public function getQualificationIds(int $id): array
    {
        return $this->employeeQualificationRepository->getQualificationIds($id);
    }

    /**
     * @param EmployeeDto $employeeDto
     * @return Employee
     * @throws \Throwable
     */
    public function create(EmployeeDto $employeeDto): Employee
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $employee = Employee::create(
                $employeeDto->getEmail(),
                $employeeDto->getPassword(),
                $employeeDto->getPhone(),
                $employeeDto->getName(),
                $employeeDto->getSecondName(),
                $employeeDto->getLastName(),
                $employeeDto->getStatus(),
                $employeeDto->getCompanyId()
            );

            $employee->balance = $employeeDto->getBalance();

            if ($this->employeeRepository->insert($employee) === null) {
                return $employee;
            }

            if ($employeeDto->photo !== null) {
                $employee = $this->saveImage($employee->id, $employeeDto->photo);
            }

            $qualificationIds = $employeeDto->getQualificationIds();

            if (!empty($qualificationIds)) {
                $this->employeeQualificationRepository->insertAll($employee->id, $qualificationIds);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $employee;
    }

    /**
     * @param int $employeeId
     * @param EmployeeDto $employeeDto
     * @return Employee
     * @throws NotFoundHttpException
     * @throws \Throwable
     */
    public function update(int $employeeId, EmployeeDto $employeeDto): Employee
    {
        /** @var Employee $employee */
        $employee = $this->employeeRepository->get($employeeId);
        if ($employee === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Employee not found'));
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $employee->email = $employeeDto->getEmail();
            $employee->phone = $employeeDto->getPhone();
            $employee->first_name = $employeeDto->getName();
            $employee->second_name = $employeeDto->getSecondName();
            $employee->last_name = $employeeDto->getLastName();
            $employee->balance = $employeeDto->getBalance();
            $employee->status = $employeeDto->getStatus();
            $employee->company_id = $employeeDto->getCompanyId();
            $employee->updated_at = new Expression('NOW()');

            if ($this->employeeRepository->update($employee) === null) {
                return $employee;
            }

            $this->employeeQualificationRepository->deleteAll($employeeId);

            $qualificationIds = $employeeDto->getQualificationIds();

            if (!empty($qualificationIds)) {
                $this->employeeQualificationRepository->insertAll($employee->id, $qualificationIds);
            }

            if ($employeeDto->photo !== null) {
                $employee = $this->saveImage($employeeId, $employeeDto->photo);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $employee;
    }

    /**
     * @param int $id
     * @return Customer|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function block(int $id): ?Employee
    {
        $employee = $this->get($id);
        $employee->status = Employee::STATUS_DELETED;
        $employee = $this->employeeRepository->update($employee);

        return $employee;
    }

    /**
     * @param int $id
     * @return Customer|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function restore(int $id): ?Employee
    {
        $employee = $this->get($id);
        $employee->status = Employee::STATUS_ACTIVE;
        $employee = $this->employeeRepository->update($employee);

        return $employee;
    }

    /**
     * @param int $id
     * @return ProfileDto|null
     */
    public function getProfile(int $id): ?ProfileDto
    {
        $employee = $this->employeeRepository->get($id);
        if ($employee === null) {
            return null;
        }

        return new ProfileDto(
            $employee->email,
            $employee->phone ?? '',
            $employee->first_name,
            $employee->second_name,
            $employee->last_name,
            $employee->getPhotoUrl(),
            $employee->getBalance(),
            $this->getFcmTokens($employee->id)
        );
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getFcmTokens(int $userId): array
    {
        return $this->pushRepository->getPushTokenListById([$userId], Push::TYPE_EMPLOYEE);
    }

    /**
     * @param int $id
     * @param ProfileEditRequest $request
     * @return ProfileDto|null
     * @throws \Throwable
     */
    public function updateProfile(int $id, ProfileEditRequest $request): ?ProfileDto
    {
        $employee = $this->employeeRepository->get($id);
        if ($employee === null) {
            return null;
        }

        $employee->email = $request->getEmail();
        $employee->phone = $request->getPhone();
        $employee->first_name = $request->getName();
        $employee->last_name = $request->getLastName();
        $employee->second_name = $request->getSecondName();

        $employee = $this->employeeRepository->update($employee);
        if ($employee === null) {
            return null;
        }

        return new ProfileDto(
            $employee->email,
            $employee->phone ?? '',
            $employee->first_name,
            $employee->second_name,
            $employee->last_name,
            $employee->getPhotoUrl(),
            $employee->getBalance(),
            $this->getFcmTokens($employee->id)
        );
    }

    /**
     * @param ProfileImageUploadRequest $request
     * @return ProfileDto|null
     * @throws BadRequestHttpException
     * @throws SafeException
     */
    public function uploadImage(ProfileImageUploadRequest $request): ?ProfileDto
    {
        $employeeId = Yii::$app->user->getId();
        if ($request->photo === null) {
            throw new BadRequestHttpException(Yii::t('errors', 'File not found.'));
        }
        $employee = $this->saveImage($employeeId, $request->photo);
        if ($employee === null) {
            return null;
        }

        return new ProfileDto(
            $employee->email,
            $employee->phone ?? '',
            $employee->first_name,
            $employee->second_name,
            $employee->last_name,
            $employee->getPhotoUrl(),
            $employee->getBalance(),
            $this->getFcmTokens($employee->id)
        );
    }

    /**
     * @param int $userId
     * @param string $token
     * @return ProfileDto|null
     */
    public function addFcmToken(int $userId, string $token): ?ProfileDto
    {
        if ($this->pushRepository->add($userId, Push::TYPE_EMPLOYEE, $token)) {
            return $this->getProfile($userId);
        }

        return null;
    }


    /**
     * @param int $userId
     * @param string $token
     * @return ProfileDto|null
     */
    public function removeFcmToken(int $userId, string $token): ?ProfileDto
    {
        if ($this->pushRepository->removeTokensById([$token])) {
            return $this->getProfile($userId);
        }

        return null;
    }


    /**
     * @param int $userId
     * @return ProfileDto|null
     */
    public function removeAllFcmTokens(int $userId): ?ProfileDto
    {
        if ($this->pushRepository->removeTokensByUserId($userId)) {
            return $this->getProfile($userId);
        }

        return null;
    }

    /**
     * @param int $employeeId
     * @param UploadedFile $uploadedFile
     * @return Employee|null
     * @throws SafeException
     */
    private function saveImage(int $employeeId, UploadedFile $uploadedFile): ?Employee
    {
        $employee = $this->employeeRepository->get($employeeId);
        if ($employee === null) {
            return null;
        }

        $oldFileName = $employee->photo;

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

        $employee = $this->employeeRepository->updatePhoto($employee, $fileName);

        if ($oldFileName) {
            $oldFilePath = UploadFileHelper::getFilePath($oldFileName);
            UploadFileHelper::deleteFile($oldFilePath);
        }

        return $employee;
    }

}
