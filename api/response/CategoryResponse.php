<?php

namespace api\response;

use common\models\Qualification;
use OpenApi\Annotations as OA;
use yii\base\Model;

/**
 * Class CategoryResponse
 * @package api\response
 * @OA\Schema(schema="CategoryResponse")
 */
class CategoryResponse extends Model
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
        parent::__construct();

        foreach ($this->attributes as $attr=>$val){
            $this->$attr = isset($qualification[$attr])?$qualification[$attr]:null;
        }
    }
}
