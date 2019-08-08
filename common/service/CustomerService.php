<?php

namespace common\service;

use api\modules\customer\v1\dto\ProfileDto;
use api\modules\customer\v1\request\ProfileEditRequest;
use api\modules\customer\v1\request\ProfileImageUploadRequest;
use common\dto\CustomerDto;
use common\helpers\UploadFileHelper;
use common\models\Customer;
use common\models\Push;
use common\repository\CustomerRepository;
use common\repository\PushRepository;
use scl\tools\rest\exceptions\SafeException;
use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class CustomerService
 * @package common\service
 */
class CustomerService
{
    /** @var CustomerRepository $customerRepository */
    private $customerRepository;
    /** @var PushRepository $pushRepository */
    private $pushRepository;

    /**
     * CustomerService constructor.
     * @param CustomerRepository $customerRepository
     * @param PushRepository $pushRepository
     */
    public function __construct(CustomerRepository $customerRepository, PushRepository $pushRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->pushRepository = $pushRepository;
    }

    /**
     * @param int $id
     * @return Customer
     * @throws NotFoundHttpException
     */
    public function get(int $id): Customer
    {
        $customer = $this->customerRepository->get($id);
        if ($customer === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Customer not found'));
        }

        return $customer;
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        $customers = $this->customerRepository->getAllList();
        return ArrayHelper::map($customers, 'id', function (Customer $model) {
            return $model->getFullName();
        });
    }

    /**
     * @param CustomerDto $customerDto
     * @return Customer
     * @throws Exception
     * @throws \Throwable
     */
    public function create(CustomerDto $customerDto): Customer
    {
        $customer = Customer::create(
            $customerDto->getEmail(),
            $customerDto->getPassword(),
            $customerDto->getPhone(),
            $customerDto->getName(),
            $customerDto->getSecondName(),
            $customerDto->getLastName(),
            $customerDto->getStatus(),
            $customerDto->getCompanyId()
        );

        if ($this->customerRepository->insert($customer) === null) {
            return $customer;
        }

        if ($customerDto->photo !== null) {
            $customer = $this->saveImage($customer->id, $customerDto->photo);
        }

        return $customer;
    }

    /**
     * @param int $customerId
     * @param CustomerDto $customerDto
     * @return Customer
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function update(int $customerId, CustomerDto $customerDto): Customer
    {
        /** @var Customer $customer */
        $customer = $this->customerRepository->get($customerId);
        if ($customer === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Customer not found'));
        }

        $customer->email = $customerDto->getEmail();
        $customer->phone = $customerDto->getPhone();
        $customer->first_name = $customerDto->getName();
        $customer->second_name = $customerDto->getSecondName();
        $customer->last_name = $customerDto->getLastName();
        $customer->status = $customerDto->getStatus();
        $customer->company_id = $customerDto->getCompanyId();
        $customer->updated_at = new Expression('NOW()');

        if ($this->customerRepository->update($customer) === null) {
            return $customer;
        }

        if ($customerDto->photo !== null) {
            $customer = $this->saveImage($customerId, $customerDto->photo);
        }

        return $customer;
    }

    /**
     * @param int $id
     * @return Customer|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function block(int $id): ?Customer
    {
        $customer = $this->get($id);
        $customer->status = Customer::STATUS_DELETED;
        $customer = $this->customerRepository->update($customer);

        return $customer;
    }

    /**
     * @param int $id
     * @return Customer|null
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function restore(int $id): ?Customer
    {
        $customer = $this->get($id);
        $customer->status = Customer::STATUS_ACTIVE;
        $customer = $this->customerRepository->update($customer);

        return $customer;
    }

    /**
     * @param int $id
     * @return ProfileDto|null
     */
    public function getProfile(int $id): ?ProfileDto
    {
        $customer = $this->customerRepository->get($id);
        if ($customer === null) {
            return null;
        }

        return new ProfileDto(
            $customer->email,
            $customer->phone ?? '',
            $customer->first_name,
            $customer->second_name,
            $customer->last_name,
            $customer->getPhotoUrl(),
            $this->getFcmTokens($customer->id)
        );
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getFcmTokens(int $userId): array
    {
        return $this->pushRepository->getPushTokenListById([$userId], Push::TYPE_CUSTOMER);
    }

    /**
     * @param int $userId
     * @param string $token
     * @return ProfileDto|null
     */
    public function addFcmToken(int $userId, string $token): ?ProfileDto
    {
        if ($this->pushRepository->add($userId, Push::TYPE_CUSTOMER, $token)) {
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
     * @param int $id
     * @param ProfileEditRequest $request
     * @return ProfileDto|null
     * @throws \Throwable
     */
    public function updateProfile(int $id, ProfileEditRequest $request): ?ProfileDto
    {
        $customer = $this->customerRepository->get($id);
        if ($customer === null) {
            return null;
        }

        $customer->email = $request->getEmail();
        $customer->phone = $request->getPhone();
        $customer->first_name = $request->getName();
        $customer->last_name = $request->getLastName();
        $customer->second_name = $request->getSecondName();

        $customer = $this->customerRepository->update($customer);
        if ($customer === null) {
            return null;
        }

        return new ProfileDto(
            $customer->email,
            $customer->phone ?? '',
            $customer->first_name,
            $customer->second_name,
            $customer->last_name,
            $customer->getPhotoUrl(),
            $this->getFcmTokens($customer->id)
        );
    }

    /**
     * @param ProfileImageUploadRequest $request
     * @return ProfileDto|null
     * @throws SafeException
     * @throws BadRequestHttpException
     */
    public function uploadImage(ProfileImageUploadRequest $request): ?ProfileDto
    {
        $customerId = Yii::$app->user->getId();
        if ($request->photo === null) {
            throw new BadRequestHttpException(Yii::t('errors', 'File not found.'));
        }
        $customer = $this->saveImage($customerId, $request->photo);
        if ($customer === null) {
            return null;
        }

        return new ProfileDto(
            $customer->email,
            $customer->phone ?? '',
            $customer->first_name,
            $customer->second_name,
            $customer->last_name,
            $customer->getPhotoUrl(),
            $this->getFcmTokens($customer->id)
        );
    }

    /**
     * @param int $customerId
     * @param UploadedFile $uploadedFile
     * @return Customer|null
     * @throws SafeException
     */
    private function saveImage(int $customerId, UploadedFile $uploadedFile): ?Customer
    {
        $customer = $this->customerRepository->get($customerId);
        if ($customer === null) {
            return null;
        }

        $oldFileName = $customer->photo;

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

        $customer = $this->customerRepository->updatePhoto($customer, $fileName);

        if ($oldFileName) {
            $oldFilePath = UploadFileHelper::getFilePath($oldFileName);
            UploadFileHelper::deleteFile($oldFilePath);
        }

        return $customer;
    }
}
