<?php

namespace api\modules\employee\v1\request;

use scl\yii\tools\Request;

/**
 * Class ProfileViewRequest
 * @package api\modules\employee\v1\request
 * @OA\Schema(schema="EmployeeProfileRequest")
 */
class ProfileViewRequest extends Request
{
    /**
     * @var string $employeeId
     * @OA\Property(type="integer")
     */
    public $employeeId;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['employeeId', 'required'],
            ['employeeId', 'integer'],
        ];
    }

    /**
     * @return string
     */
    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }
}
