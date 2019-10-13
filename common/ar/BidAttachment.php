<?php

namespace common\ar;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Class BidAttachment
 * @package common\models
 * @property string $bid_id [integer]
 * @property string $type [integer]
 * @property string $name [varchar(255)]
 * @property string $original_name [varchar(255)]
 */
class BidAttachment extends ActiveRecord
{
    const TYPE_PHOTO_CUSTOMER = 10;
    const TYPE_PHOTO_EMPLOYEE = 30;
    const TYPE_FILE = 20;

    const PATH_IMAGES = '/images/';
    const PATH_FILES = '/files/';

    const MAX_FILES = 3;
    const MAX_PHOTOS_CUSTOMER = 5;
    const MAX_PHOTOS_EMPLOYEE = 5;

    const MAX_PHOTO_SIZE = 2048;
    const MAX_PHOTO_SIZE_BYTES = self::MAX_PHOTO_SIZE * 1000;
    const MAX_FILE_SIZE = 1024 * 12;
    const MAX_FILE_SIZE_BYTES = self::MAX_FILE_SIZE * 1000;

    const TYPES = [
        self::TYPE_PHOTO_CUSTOMER,
        self::TYPE_PHOTO_EMPLOYEE,
        self::TYPE_FILE,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%bid_attachment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['bid_id', 'type', 'name'], 'required'],
            [['bid_id', 'type'], 'integer'],
            [['name', 'original_name'], 'string'],
            ['type', 'in', 'range' => self::TYPES],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'bid_id' => Yii::t('app', 'Bid ID'),
            'type' => Yii::t('app', 'File Type'),
            'name' => Yii::t('app', 'File Name'),
            'original_name' => Yii::t('app', 'Original File Name'),
        ];
    }

    /**
     * @param int $type
     * @return string|null
     */
    public function getFileUrl(int $type)
    {
        if (empty($this->name)) {
            return null;
        }

        $typeIsPhoto = $type === self::TYPE_PHOTO_CUSTOMER || $type === self::TYPE_PHOTO_EMPLOYEE;

        return (string)(Url::base(true) . ($typeIsPhoto ? self::PATH_IMAGES : self::PATH_FILES) . $this->name);
    }
}
