<?php

namespace api\modules\customer\v1\request;

use scl\yii\tools\Request;

/**
 * Class WorkListRequest
 * @package api\modules\customer\v1\request
 * @OA\Schema(schema="CustomerWorkListRequest")
 */
class WorkListRequest extends Request
{
    /**
     * @var null|string $category
     * @OA\Property(type="integer")
     */
    public $category;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['category', 'integer'],
        ];
    }

    /**
     * @return null|string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }
}
