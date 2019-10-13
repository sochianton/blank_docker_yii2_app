<?php

namespace api\response;

use common\models\Qualification;
use OpenApi\Annotations as OA;

/**
 * Class CategoryListResponse
 * @package api\response
 * @OA\Schema(schema="CategoryListResponse")
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
        $this->categories = array_map(function($arr){
            return new CategoryResponse($arr);
        }, $categories);
    }
}
