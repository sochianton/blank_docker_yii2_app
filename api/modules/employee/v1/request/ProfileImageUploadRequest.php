<?php

namespace api\modules\employee\v1\request;

use api\misc\UploadedFileBase64;
use common\models\BidAttachment;
use scl\yii\tools\Request;
use Yii;
use yii\web\UploadedFile;

/**
 * Class UploadImageRequest
 * @package api\modules\employee\v1\request
 * @OA\Schema(schema="EmployeeProfileImageUploadRequest")
 */
class ProfileImageUploadRequest extends Request
{
    /**
     * @var UploadedFile $photo
     * @OA\Property(type="object",
     *     @OA\Property(
     *        property="name",
     *        description="Original filename.",
     *        type="string"
     *     ),
     *     @OA\Property(
     *        property="content",
     *        description="Base64 encoded file data.",
     *        type="string"
     *     )
     * ),
     */
    public $photo;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                'photo',
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
        ];
    }
}
