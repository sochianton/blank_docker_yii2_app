<?php

namespace api\modules\employee\v1\request;

use api\misc\UploadedFileBase64;
use common\models\BidAttachment;
use OpenApi\Annotations as OA;
use scl\yii\tools\Request;
use Yii;

/**
 * Class BidDoneRequest
 * @package api\modules\employee\v1\request
 * @OA\Schema(schema="EmployeeBidDoneRequest")
 */
class BidDoneRequest extends Request
{
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $comment;
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
     * @return array
     */
    public function rules(): array
    {
        return [
            ['comment', 'string', 'max' => 500],
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
        ];
    }

    /**
     * @param array $params
     */
    public function fillFromRequest($params)
    {
        $this->load($params, '');
        $this->photos = UploadedFileBase64::getInstancesByName('photos');
    }

    /**
     * @return null|string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return array
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }
}
