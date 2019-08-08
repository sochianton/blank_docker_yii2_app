<?php

namespace backend\models\forms;

use common\dto\EmployeeDto;
use common\models\BidAttachment;
use common\models\Company;
use common\models\Employee;
use common\models\Qualification;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class EmployeeForm
 * @package backend\models\forms
 */
class EmployeeForm extends Model
{
    /**
     * @var int $id
     */
    public $id;
    /**
     * @var string $name
     */
    public $name;
    /**
     * @var string $secondName
     */
    public $secondName;
    /**
     * @var string $lastName
     */
    public $lastName;
    /**
     * @var int $balance
     */
    public $balance;
    /**
     * @var string $phone
     */
    public $phone;
    /**
     * @var string $email
     */
    public $email;
    /**
     * @var string $password
     */
    public $password;
    /**
     * @var string $passwordRepeat
     */
    public $passwordRepeat;
    /**
     * @var int $status
     */
    public $status;
    /**
     * @var int
     */
    public $companyId;
    /**
     * @var UploadedFile $photo
     */
    public $photo;
    /**
     * @var array $qualifications
     */
    public $qualifications;


    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['balance'], 'integer', 'min' => 0],
            [['email'], 'email'],
            [['email', 'name', 'secondName', 'lastName', 'status', 'companyId'], 'required'],
            [['name', 'secondName', 'lastName'], 'string', 'max' => 100],
            ['password', 'required', 'on' => 'create'],
            [['password', 'passwordRepeat'], 'string', 'min' => 6],
            [
                'passwordRepeat',
                'compare',
                'compareAttribute' => 'password',
                'message' => Yii::t('app', 'Passwords don\'t match')
            ],
            ['status', 'in', 'range' => array_keys(User::STATUSES)],
            ['status', 'default', 'value' => User::STATUS_ACTIVE],
            ['phone', 'string', 'min' => 10, 'max' => 20],
            [
                'photo',
                'image',
                'maxSize' => BidAttachment::MAX_PHOTO_SIZE_BYTES,
                'tooBig' => Yii::t('app', 'File size limit is 2MB')
            ],
            [['companyId'], 'exist', 'targetClass' => Company::class, 'targetAttribute' => 'id'],
            [
                'qualifications',
                'each',
                'rule' => [
                    'exist',
                    'targetClass' => Qualification::class,
                    'targetAttribute' => 'id'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('app', 'First Name'),
            'secondName' => Yii::t('app', 'Second Name'),
            'lastName' => Yii::t('app', 'Last Name'),
            'phone' => Yii::t('app', 'Phone'),
            'password' => Yii::t('app', 'Password'),
            'passwordRepeat' => Yii::t('app', 'Repeat password'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'companyId' => Yii::t('app', 'Company'),
            'photo' => Yii::t('app', 'Photo'),
            'qualifications' => Yii::t('app', 'Qualifications'),
            'balance' => Yii::t('app', 'Balance'),
        ];
    }

    /**
     * @param array $data
     * @param null $formName
     * @return bool
     */
    public function load($data, $formName = null): bool
    {
        $load = parent::load($data, $formName);
        $this->photo = UploadedFile::getInstance($this, 'photo');
        return $load;
    }

    /**
     * @param Employee $customer
     * @param array $qualifications
     */
    public function fillFromModel(Employee $customer, array $qualifications): void
    {
        $this->id = $customer->id;
        $this->phone = $customer->phone;
        $this->email = $customer->email;
        $this->name = $customer->first_name;
        $this->secondName = $customer->second_name;
        $this->lastName = $customer->last_name;
        $this->status = $customer->status;
        $this->companyId = $customer->company_id;
        $this->balance = $customer->balance;
        $this->qualifications = $qualifications;
    }

    /**
     * @return EmployeeDto
     */
    public function getDto(): EmployeeDto
    {
        return new EmployeeDto(
            (string)$this->phone,
            (string)$this->email,
            (string)$this->password,
            (string)$this->name,
            (string)$this->secondName,
            (string)$this->lastName,
            (int)$this->balance,
            (int)$this->status,
            (int)$this->companyId,
            $this->photo,
            array_filter((array)$this->qualifications)
        );
    }
}
