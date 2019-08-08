<?php

namespace backend\models\search;

use common\models\Qualification;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * QualificationSearch represents the model behind the search form of `common\models\Qualification`.
 */
class QualificationSearch extends Model
{
    public $id;
    public $name;
    public $createdAt;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'createdAt'], 'safe'],
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
        $query = Qualification::find();

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

        $query->andFilterWhere(['ilike', 'name', $this->name]);

        $query->andFilterWhere(['=', 'id', $this->id]);

        return $dataProvider;
    }
}
