<?php


namespace common\assets;


use yii\bootstrap\BootstrapPluginAsset;
use yii\web\JqueryAsset;

class LteAsset extends BaseAsset
{

    public $css = [
        'lte/css/font-awesome.min.css',
        'lte/css/AdminLTE.min.css',
        'lte/css/skins/skin-blue-light.min.css',
        'lte/plugins/iCheck/square/blue.css',

        '//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic',

    ];

    public $js = [
        'lte/js/adminlte.min.js',
        'lte/plugins/iCheck/icheck.min.js',
    ];

    public $depends = [
        JqueryAsset::class,
        BootstrapPluginAsset::class,
    ];

}