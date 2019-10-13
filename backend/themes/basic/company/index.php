<?php

use backend\models\search\CompanySearch;
use \common\service\AdmUserService;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel CompanySearch */

$this->title = Yii::t('app', 'Companies');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">
    <?=AdmUserService::getGridWidget($dataProvider, $searchModel)->run()?>

</div>
