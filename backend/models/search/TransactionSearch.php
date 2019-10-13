<?php

namespace backend\models\search;

use common\service\TransactionService;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\di\NotInstantiableException;

/**
 * Class TransactionSearch
 * @package backend\models\search
 */
class TransactionSearch extends Model
{
    /**
     * @var string
     */
    public $customer;
    /**
     * @var string
     */
    public $employee;
    /**
     * @var int
     */
    public $bidId;
    /**
     * @var int
     */
    public $price;
    /**
     * @var int
     */
    public $commission;
    /**
     * @var string
     */
    public $dateStart;
    /**
     * @var string
     */
    public $dateEnd;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['dateStart', 'dateEnd', 'customer', 'employee'], 'string'],
            [['bidId', 'price', 'commission'], 'integer'],
            [['customer', 'employee', 'price', 'commission', 'dateStart', 'dateEnd'], 'default', 'value' => null],
            [['dateStart'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['dateEnd'], 'datetime', 'format' => 'php:Y-m-xd H:i:s'],
        ];
    }


    private function validatePeriod()
    {

        try{
            $start = new \DateTime($this->dateStart);
            $end = new \DateTime($this->dateEnd);
        }
        catch (\Exception $e){
            $start = new \DateTime();
            $end = new \DateTime();
        }

        $period = $start->diff($end);


        if ($this->dateEnd && $this->dateStart && $period->days > TransactionService::DAYS ) {
            $this->addError('dateEnd',
                Yii::t('errors', 'Select period less than one month. For large periods use export.'));
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'customer' => Yii::t('app', 'Customer'),
            'employee' => Yii::t('app', 'Employee'),
            'bidId' => Yii::t('app', 'Bid ID'),
            'price' => Yii::t('app', 'Price'),
            'commission' => Yii::t('app', 'Commission'),
            'dateStart' => Yii::t('app', 'Date Start'),
            'dateEnd' => Yii::t('app', 'Date End'),
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
     * @param bool $isPost
     * @return ArrayDataProvider
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    public function search(array $params, bool $isPost): ArrayDataProvider
    {
        $dataProvider = new ArrayDataProvider([
            'allModels' => [],
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->load($params);

        if (!$this->validate() || (!$isPost && !$this->validatePeriod())) {
            return $dataProvider;
        }
        /** @var TransactionService $transactionService */
        $transactionService = Yii::$container->get(TransactionService::class);

        $transactions = $transactionService->getList(
            $this->dateStart,
            $this->dateEnd,
            null,
            $this->customer,
            $this->employee,
            $this->price,
            $this->commission,
            $this->bidId ?: null
        );
        $dataProvider->allModels = $transactions;

        return $dataProvider;
    }
}
