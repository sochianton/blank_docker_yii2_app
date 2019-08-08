<?php

namespace backend\models\search;

use common\models\Bid;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class BidSearch
 * @package backend\models\search
 */
class BidSearch extends Model
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
     * @var int $status
     */
    public $status;
    /**
     * @var int $price
     */
    public $price;
    /**
     * @var string
     */
    public $object;
    /**
     * @var string $completeAt
     */
    public $completeAt;
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
            [['id', 'price'], 'integer'],
            [['name', 'status', 'object', 'completeAt', 'createdAt'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'status' => Yii::t('app', 'Status'),
            'price' => Yii::t('app', 'Price'),
            'object' => Yii::t('app', 'Object'),
            'completeAt' => Yii::t('app', 'Complete At'),
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
        $query = Bid::find();

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
        $completeAtStart = null;
        $completeAtEnd = null;

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->createdAt) && strpos($this->createdAt, ' - ') !== false) {
            [$createdAtStart, $createdAtEnd] = explode(' - ', $this->createdAt);

            $startDate = (new \DateTime($createdAtStart))->getTimestamp();
            $endDate = $createdAtStart === $createdAtEnd
                ? (new \DateTime($createdAtEnd))->modify('+ 1 day last second')->getTimestamp() // add 1 day
                : (new \DateTime($createdAtEnd))->getTimestamp();

            $query->andFilterWhere([
                'between',
                'created_at',
                $startDate,
                $endDate
            ]);
        }

        if (!empty($this->completeAt) && strpos($this->completeAt, ' - ') !== false) {
            [$completeAtStart, $completeAtEnd] = explode(' - ', $this->completeAt);

            $startDate = (new \DateTime($completeAtStart))->getTimestamp();
            $endDate = $completeAtStart === $completeAtEnd
                ? (new \DateTime($completeAtEnd))->modify('+ 1 day last second')->getTimestamp() // add 1 day
                : (new \DateTime($completeAtEnd))->getTimestamp();

            $query->andFilterWhere([
                'between',
                'complete_at',
                $startDate,
                $endDate
            ]);
        }

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'object', $this->object])
            ->andFilterWhere(['=', 'price', $this->price])
            ->andFilterWhere(['=', 'status', $this->status])
            ->andFilterWhere(['=', 'id', $this->id]);

        return $dataProvider;
    }
}
