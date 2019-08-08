<?php

namespace backend\models\search;

use common\models\Employee;
use common\repository\EmployeeQualificationRepository;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class EmployeeSearch
 * @package backend\models\search
 */
class EmployeeSearch extends Model
{
    /**
     * @var int $id
     */
    public $id;
    /**
     * @var string $email
     */
    public $email;
    /**
     * @var string $phone
     */
    public $phone;
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
     * @var int $status
     */
    public $status;
    /**
     * @var array $qualifications
     */
    public $qualifications;
    /**
     * @var int $balance
     */
    public $balance;
    /**
     * @var string $createdAt
     */
    public $createdAt;


    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'status', 'balance'], 'integer'],
            [['email', 'phone', 'name', 'secondName', 'lastName'], 'string'],
            [['createdAt'], 'safe'],
            [['qualifications'], 'each', 'rule' => ['integer']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'email' => Yii::t('app', 'Email'),
            'phone' => Yii::t('app', 'Phone'),
            'name' => Yii::t('app', 'First Name'),
            'secondName' => Yii::t('app', 'Second Name'),
            'lastName' => Yii::t('app', 'Last Name'),
            'status' => Yii::t('app', 'Status'),
            'qualifications' => Yii::t('app', 'Qualifications'),
            'balance' => Yii::t('app', 'Balance'),
            'createdAt' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \Exception
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Employee::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC
                ]
            ]
        ]);

        $createdAtStart = null;
        $createdAtEnd = null;

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->createdAt) && strpos($this->createdAt, ' - ') !== false) {
            [$createdAtStart, $createdAtEnd] = explode(' - ', $this->createdAt);

            $startDate = (new \DateTime($createdAtStart))->format('Y-m-d H:i:s');
            $endDate = $createdAtStart === $createdAtEnd
                ? (new \DateTime($createdAtEnd))->modify('+ 1 day last second')->format('Y-m-d H:i:s') // add 1 day
                : (new \DateTime($createdAtEnd))->format('Y-m-d H:i:s');

            $query->andFilterWhere([
                'between',
                'created_at',
                $startDate,
                $endDate
            ]);
        }

        if (is_array($this->qualifications)) {
            /** @var EmployeeQualificationRepository $employeeQualificationRepository */
            $employeeQualificationRepository = Yii::$container->get(EmployeeQualificationRepository::class);
            $employeeIds = $employeeQualificationRepository->getEmployeeIdsByQualifications($this->qualifications);
            $query->andFilterWhere(['id' => $employeeIds]);
        }

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['ilike', 'first_name', $this->name])
            ->andFilterWhere(['ilike', 'second_name', $this->secondName])
            ->andFilterWhere(['ilike', 'last_name', $this->lastName])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'balance', $this->balance])
            ->andFilterWhere(['=', 'id', $this->id]);

        return $dataProvider;
    }
}
