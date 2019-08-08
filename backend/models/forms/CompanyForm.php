<?php

namespace backend\models\forms;

use common\dto\CompanyDto;
use common\models\Company;
use Yii;
use yii\base\Model;

/**
 * Class CompanyForm
 * @package backend\models\forms
 */
class CompanyForm extends Model
{
    /**
     * @var int $id
     */
    public $id;
    /**
     * @var int $type
     */
    public $type;
    /**
     * @var int $status
     */
    public $status;
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var string $address
     */
    public $address;
    /**
     * @var integer $numberOfContract
     */
    public $numberOfContract;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['type', 'name', 'status', 'address'], 'required'],
            [['id', 'type', 'status', 'numberOfContract'], 'integer'],
            [['name', 'address'], 'string', 'max' => 100],
            ['status', 'in', 'range' => array_keys(Company::STATUSES)],
            ['type', 'in', 'range' => array_keys(Company::TYPES)],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'name' => Yii::t('app', 'Name'),
            'address' => Yii::t('app', 'Address'),
            'numberOfContract' => Yii::t('app', 'Number Of Contract'),
        ];
    }

    /**
     * @param Company $customer
     */
    public function fillFromModel(Company $customer): void
    {
        $this->id = $customer->id;
        $this->type = $customer->type;
        $this->status = $customer->status;
        $this->name = $customer->name;
        $this->address = $customer->address;
        $this->numberOfContract = $customer->number_of_contract;
    }

    /**
     * @return CompanyDto
     */
    public function getDto(): CompanyDto
    {
        return new CompanyDto(
            (int)$this->type,
            (int)$this->status,
            (string)$this->name,
            (string)$this->address,
            (int)$this->numberOfContract
        );
    }
}
