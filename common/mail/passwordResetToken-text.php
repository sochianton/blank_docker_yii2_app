<?php

/* @var $this yii\web\View */
/* @var $user common\models\Admin */

?>
<?= Yii::t('app', 'Hello') . ' ' . $user->getFullName() ?>,
<?= Yii::t('app', 'Your new login details') ?>:
<?= Yii::t('app', 'Username') ?> : <?= $user->email ?>
<?= Yii::t('app', 'Password') ?> : <?= $password ?? '' ?>
