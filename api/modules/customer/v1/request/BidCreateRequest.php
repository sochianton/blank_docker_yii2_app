<?php

namespace api\modules\customer\v1\request;

use api\misc\UploadedFileBase64;
use common\dto\BidDto;
use common\models\Bid;
use common\models\BidAttachment;
use scl\yii\tools\Request;
use Yii;

/**
 * Class BidCreateRequest
 * @package api\modules\customer\v1\request
 * @OA\Schema(schema="CustomerBidCreateRequest")
 */
class BidCreateRequest extends Request
{
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $name;
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $price;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $object;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $comment;
    /**
     * @var int
     * @OA\Property(type="string")
     */
    public $completeAt;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="integer"))
     */
    public $works;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object",
     *     @OA\Property(
     *        property="name",
     *        description="Original filename.",
     *        type="string"
     *     ),
     *     @OA\Property(
     *        property="content",
     *        description="Base64 encoded file data.",
     *        type="string"
     *    )
     * ))
     */
    public $photos;
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object",
     *     @OA\Property(
     *        property="name",
     *        description="Original filename.",
     *        type="string"
     *     ),
     *     @OA\Property(
     *        property="content",
     *        description="Base64 encoded file data.",
     *        type="string"
     *    )
     * ))
     */
    public $files;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['name', 'object'], 'required'],
            [['price', 'completeAt'], 'integer'],
            [['name', 'object'], 'string', 'max' => 255],
            [['comment'], 'string', 'max' => 500],
            ['works', 'each', 'rule' => ['integer']],
            [
                'photos',
                'each',
                'rule' => [
                    function ($attribute) {
                        /** @var UploadedFileBase64 $uploadedFile */
                        $uploadedFile = $this->$attribute;
                        if (empty($uploadedFile->name)) {
                            $this->addError($attribute, Yii::t('app', 'Name of file cannot be empty!'));
                            return false;
                        }
                        if (!$uploadedFile->isValidBase64()) {
                            $this->addError($attribute, Yii::t('app', 'Content of file must be a valid base64 data'));
                            return false;
                        }
                        if ($uploadedFile->size >= BidAttachment::MAX_PHOTO_SIZE_BYTES) {
                            $this->addError($attribute, Yii::t('app', 'File size limit is 2MB'));
                            return false;
                        }
                        return true;
                    }
                ]
            ],
            [
                'files',
                'each',
                'rule' => [
                    function ($attribute) {
                        /** @var UploadedFileBase64 $uploadedFile */
                        $uploadedFile = $this->$attribute;
                        if (empty($uploadedFile->name)) {
                            $this->addError($attribute, Yii::t('app', 'Name of file cannot be empty!'));
                            return false;
                        }
                        if (!$uploadedFile->isValidBase64()) {
                            $this->addError($attribute, Yii::t('app', 'Content of file must be a valid base64 data'));
                            return false;
                        }
                        if ($uploadedFile->size >= BidAttachment::MAX_FILE_SIZE_BYTES) {
                            $this->addError($attribute, Yii::t('app', 'File size limit is 12MB'));
                            return false;
                        }
                        return true;
                    }
                ]
            ]
        ];
    }

    public function fillFromRequest($params)
    {
        $this->load($params, '');
        $this->photos = UploadedFileBase64::getInstancesByName('photos');
        $this->files = UploadedFileBase64::getInstancesByName('files');
    }

    /**
     * @return BidDto
     */
    public function getDto(): BidDto
    {
        $userId = Yii::$app->user->getId();

        return new BidDto(
            0,
            (string)$this->name,
            (int)$userId,
            null,
            Bid::STATUS_NEW,
            (int)$this->price,
            (string)$this->object,
            (string)$this->comment,
            null,
            (int)$this->completeAt,
            time(),
            time(),
            (array)$this->works,
            (array)$this->photos,
            (array)$this->files
        );
    }
}
