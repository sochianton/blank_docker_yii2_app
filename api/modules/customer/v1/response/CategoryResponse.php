<?php

namespace api\modules\customer\v1\response;

use common\models\Qualification;
use OpenApi\Annotations as OA;

/**
 * Class CategoryResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerCategoryResponse")
 */
class CategoryResponse
{
    /**
     * @var int
     * @OA\Property(type="integer")
     */
    public $id;
    /**
     * @var int
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * CategoryResponse constructor.
     * @param array $qualification
     */
    public function __construct(array $qualification)
    {
        $this->id = (int)$qualification['id'] ?? 0;
        $this->name = (string)$qualification['name'] ?? '';
    }
}
