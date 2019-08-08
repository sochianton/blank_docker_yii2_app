<?php

namespace backend\models\search;

use common\models\Company;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CompanySearch represents the model behind the search form of `common\models\Company`.
 */
class CompanySearch extends Model
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
     * @var string $createdAt
     */
    public $createdAt;
    /**
     * @var string $updatedAt
     */
    public $updatedAt;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'type', 'status', 'numberOfContract'], 'integer'],
            [['name', 'address'], 'string', 'max' => 100],
            [['type', 'status', 'name', 'address', 'createdAt', 'updatedAt'], 'safe'],
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
            'address' => Yii::t('app', 'Address'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
            'numberOfContract' => Yii::t('app', 'Number Of Contract'),
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
        $query = Company::find();

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

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'address', $this->address])
            ->andFilterWhere(['=', 'number_of_contract', $this->numberOfContract])
            ->andFilterWhere(['=', 'type', $this->type])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'id', $this->id]);

        return $dataProvider;
    }
}
