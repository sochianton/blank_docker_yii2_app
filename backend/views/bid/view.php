<?php

use common\models\Bid;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Bid */
/* @var $customerPhotos array */
/* @var $employeePhotos array */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="bid-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <? /*= empty($model->deleted_at) ? Html::a(Yii::t('app', 'Block'), [
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
        */ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'customer_id',
            'employee_id',
            [
                'attribute' => 'status',
                'value' => function (Bid $model) {
                    return Yii::t('app', Bid::STATUSES[$model->status] ?? null);
                }
            ],
            [
                'employeePhotos' => Yii::t('app', 'Employee Photos'),
                'label' => Yii::t('app', 'Customer Photos'),
                'format' => 'raw',
                'value' => function () use ($customerPhotos) {
                    return implode('&nbsp;', array_map(function ($customerPhoto) {
                        return Html::img($customerPhoto['url'] ?? '', ['width' => '200px']);
                    }, $customerPhotos));
                }
            ],
            [
                'label' => Yii::t('app', 'Employee Photos'),
                'format' => 'raw',
                'value' => function () use ($employeePhotos) {
                    return implode('&nbsp;', array_map(function ($employeePhoto) {
                        return Html::img($employeePhoto['url'] ?? '', ['width' => '200px']);
                    }, $employeePhotos));
                }
            ],
            'price',
            'object',
            'customer_comment',
            'employee_comment',
            [
                'attribute' => 'complete_at',
                'format' => 'dateTime',
            ],
            [
                'attribute' => 'created_at',
                'format' => 'dateTime',
            ],
        ],
    ]) ?>

</div>
