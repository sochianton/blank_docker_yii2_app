<?php

namespace backend\models\search;

use common\models\Customer;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class CustomerSearch
 * @package backend\models\search
 */
class CustomerSearch extends Model
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
     * @var string $createdAt
     */
    public $createdAt;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'status'], 'integer'],
            [['email', 'phone', 'name', 'secondName', 'lastName'], 'string'],
            [['createdAt'], 'safe'],
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
        $query = Customer::find();

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

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['ilike', 'first_name', $this->name])
            ->andFilterWhere(['ilike', 'second_name', $this->secondName])
            ->andFilterWhere(['ilike', 'last_name', $this->lastName])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'id', $this->id]);

        return $dataProvider;
    }
}
