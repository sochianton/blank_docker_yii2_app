<?php

namespace backend\models\search;

use common\models\Work;
use common\repository\EmployeeQualificationRepository;
use common\repository\WorkQualificationRepository;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WorkSearch represents the model behind the search form of `common\models\Work`.
 */
class WorkSearch extends Model
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
     * @var string $createdAt
     */
    public $createdAt;
    /**
     * @var array $qualifications
     */
    public $qualifications;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'createdAt'], 'safe'],
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
            'name' => Yii::t('app', 'Name'),
            'qualifications' => Yii::t('app', 'Categories'),
            'createdAt' => Yii::t('app', 'Created At'),
            'updatedAt' => Yii::t('app', 'Updated At'),
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
        $query = Work::find();

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

        if (is_array($this->qualifications)) {
            /** @var WorkQualificationRepository $workQualificationRepository */
            $workQualificationRepository = Yii::$container->get(WorkQualificationRepository::class);
            $employeeIds = $workQualificationRepository->getWorkIdsByQualifications($this->qualifications);
            $query->andFilterWhere(['id' => $employeeIds]);
        }

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['=', 'id', $this->id]);

        return $dataProvider;
    }
}
