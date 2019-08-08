<?php

/* @var $this yii\web\View */
/* @var $user common\models\Admin */

?>
<div class="password-reset">
    <p><?= Yii::t('app', 'Hello') . ' ' . $user->getFullName() ?>,</p>
    <p><?= Yii::t('app', 'Your new login details') ?>:</p>
    <p><?= Yii::t('app', 'Username') ?> : <?= $user->email ?></p>
    <p><?= Yii::t('app', 'Password') ?> : <?= $password ?? '' ?></p>
</div>
