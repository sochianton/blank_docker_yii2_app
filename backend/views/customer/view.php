<?php

use common\models\Customer;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Customers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="customer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= $model->status === Customer::STATUS_ACTIVE ? Html::a(Yii::t('app', 'Block'), [
            'block',
            'id' => $model->id,
        ], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to block this item?'),
                'method' => 'post',
            ],
        ]) : Html::a(Yii::t('app', 'Restored'), ['restore', 'id' => $model->id], [
            'class' => 'btn btn-info',
        ])
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'photo',
                'format' => 'raw',
                'value' => function (Customer $model) {
                    return Html::img($model->getPhotoUrl(), ['width' => '200px']);
                }
            ],
            'email:email',
            [
                'attribute' => 'phone',
                'value' => function (Customer $model) {
                    return $model->getPhoneString();
                }
            ],
            'first_name',
            'second_name',
            'last_name',
            [
                'attribute' => 'status',
                'value' => function (Customer $model) {
                    return Yii::t('app', Customer::STATUSES[$model->status] ?? null);
                }
            ],
            'created_at',
        ],
    ]) ?>

</div>
