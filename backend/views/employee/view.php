<?php

use common\models\Employee;
use common\models\Qualification;
use common\service\EmployeeService;
use common\service\QualificationService;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Employee */
/* @var $employeeService EmployeeService */
/* @var $qualificationService QualificationService */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Employees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="employee-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= $model->status === Employee::STATUS_ACTIVE ? Html::a(Yii::t('app', 'Block'), [
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
                'value' => function (Employee $model) {
                    return Html::img($model->getPhotoUrl(), ['width' => '200px']);
                }
            ],
            [
                'attribute' => 'balance',
                'value' => function (Employee $model) {
                    return $model->getBalance();
                }
            ],
            'email:email',
            [
                'attribute' => 'phone',
                'value' => function (Employee $model) {
                    return $model->getPhoneString();
                }
            ],
            'first_name',
            'second_name',
            'last_name',
            [
                'attribute' => 'status',
                'value' => function (Employee $model) {
                    return Yii::t('app', Employee::STATUSES[$model->status] ?? null);
                }
            ],
            [
                'label' => Yii::t('app', 'Qualifications'),
                'format' => 'raw',
                'value' => function (Employee $model) use ($employeeService, $qualificationService) {
                    $employeeQualificationIds = $employeeService->getQualificationIds($model->id);
                    if (empty($employeeQualificationIds)) {
                        return null;
                    }
                    $qualifications = $qualificationService->getList(false, $employeeQualificationIds);
                    $list = array_map(function (Qualification $qualification) {
                        return $qualification->name;
                    }, $qualifications);
                    sort($list);
                    return implode(', ', $list);
                }
            ],
            'created_at',
        ],
    ]) ?>

</div>
