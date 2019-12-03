<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 20.10.17
 * Time: 16:34
 *
 *
 * @var $this \yii\web\View
 *
 */

use common\service\AdmUserService;
use \yii\helpers\Html;
use yii\helpers\Url;

/** @var \common\ar\User $user */
$user = Yii::$app->user->getModel();
?>
<!-- Logo -->
<a href="<?=Yii::$app->user->returnUrl?>" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b><?=Yii::$app->name?></b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><b><?=Yii::$app->name?></b></span>

</a>
<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </a>

    <div class="navbar-custom-menu">

        <?=\yii\widgets\Menu::widget([
            'options'=>[
                'class' => 'nav navbar-nav',
            ],
            'encodeLabels' => false,

            'items' => [
                [
                    'label' => '<img src="'.$user->getPhotoUrl().'" class="user-image" alt="User Image"><span class="hidden-xs">'.$user->getFullName().'</span>',
                    'url' => '#',
                    'template' => "<a href='{url}' class='dropdown-toggle' aria-expanded='false'>{label}</a>",
                    'options' => [
                        'class' => 'user user-menu'
                    ],
                    'visible' => !Yii::$app->user->isGuest
                ],
                [
                    'label' => '<i class="fa fa-sign-out"></i>
                        <span>'.Yii::t('app', 'Exit').'</span>',
                    //'label' => Yii::t('app', 'Exit'),
                    'url' => Url::toRoute(['/site/logout']),
                    'template' => "<a href='{url}' class='' data-method='POST'>{label}</a>",
                    'options' => [
                        //'class' => 'dropdown'
                    ],
                    'visible' => !Yii::$app->user->isGuest
                ],
            ]
        ])?>
    </div>
</nav>