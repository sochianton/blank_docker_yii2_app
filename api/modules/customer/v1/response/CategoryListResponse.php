<?php

namespace api\modules\customer\v1\response;

use common\models\Qualification;
use OpenApi\Annotations as OA;

/**
 * Class CategoryListResponse
 * @package api\modules\customer\v1\response
 * @OA\Schema(schema="CustomerCategoryListResponse")
 */
class CategoryListResponse
{
    /**
     * @var array
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/CustomerCategoryResponse"))
     */
    public $categories;

    /**
     * CategoryListResponse constructor.
     * @param array $categories
     */
    public function __construct(array $categories)
    {
        $this->categories = array_map(function (Qualification $qualification) {
            return new CategoryResponse($qualification->toArray());
        }, $categories);
    }
}
