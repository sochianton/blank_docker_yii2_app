<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 20.10.17
 * Time: 16:33
 */

use yii\helpers\Html;

\backend\assets\BackendAsset::register($this);

?>

<meta charset="<?= Yii::$app->charset ?>"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<?=Html::csrfMetaTags()?>
<title><?=Yii::t('app', 'Page not found')?></title>


<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->


<?php $this->head() ?>
