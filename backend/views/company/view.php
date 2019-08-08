<?php

use common\models\Company;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Company */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Companies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="company-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= $model->status === Company::STATUS_ACTIVE ? Html::a(Yii::t('app', 'Block'), [
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
            'name',
            [
                'attribute' => 'type',
                'value' => function (Company $model) {
                    return Yii::t('app', Company::TYPES[$model->type] ?? null);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function (Company $model) {
                    return Yii::t('app', Company::STATUSES[$model->status] ?? null);
                }
            ],
            'number_of_contract',
            'address',
            'created_at'
        ],
    ]) ?>

</div>
