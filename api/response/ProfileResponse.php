<?php

namespace api\response;

use OpenApi\Annotations as OA;
use yii\base\Model;

/**
 * Class ProfileResponse
 * @package api\response
 * @OA\Schema(schema="ProfileResponse")
 */
class ProfileResponse extends Model
{
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $email;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $phone;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $name;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $secondName;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $lastName;
    /**
     * @var string|null
     * @OA\Property(type="string")
     */
    public $photo;
    /**
     * @var float
     * @OA\Property(type="number")
     */
    public $balance;
    /**
     * @var array
     * @OA\Property(
     *     type="array",
     *     @OA\Items(type="string")
     * )
     */
    public $fcmTokens;

    /**
     * ProfileResponse constructor.
     * @param array $profile
     */
    public function __construct(array $profile)
    {
        foreach ($this->attributes as $attr=>$val){
            $this->$attr = isset($profile[$attr])?$profile[$attr]:null;
        }
    }
}
