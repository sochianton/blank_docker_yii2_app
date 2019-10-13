<?php

/* @var $this yii\web\View */

use \common\services\UserService;
use \yii\widgets\Menu;


?>
<?=Menu::widget([
    'options'=>[
        'class' => 'sidebar-menu',
        'data-widget' => 'tree',
    ],
    'submenuTemplate' => "\n<ul class='treeview-menu'>\n{items}\n</ul>\n",
    'encodeLabels' => false,

    'items' => UserService::getCurUserMenu(),
])?>