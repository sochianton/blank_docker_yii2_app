<?php

use common\models\Qualification;
use common\models\Work;
use common\service\QualificationService;
use common\service\WorkService;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Work */
/* @var $workService WorkService */
/* @var $qualificationService QualificationService */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Works'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qualification-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?= empty($model->deleted_at) ? Html::a(Yii::t('app', 'Block'), [
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
                'label' => Yii::t('app', 'Categories'),
                'format' => 'raw',
                'value' => function (Work $model) use ($workService, $qualificationService) {
                    $qualificationIds = $workService->getQualificationIds($model->id);
                    if (empty($qualificationIds)) {
                        return null;
                    }
                    $qualifications = $qualificationService->getList(false, $qualificationIds);
                    $list = array_map(function (Qualification $qualification) {
                        return $qualification->name;
                    }, $qualifications);
                    sort($list);
                    return implode(', ', $list);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'dateTime',
            ],
            [
                'attribute' => 'deleted_at',
                'format' => 'dateTime',
            ]
        ],
    ]) ?>

</div>
