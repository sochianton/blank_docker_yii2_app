<?php

namespace common\assets;

//use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;
use yii\widgets\MaskedInputAsset;


/**
 * Created by PhpStorm.
 * User: anton
 * Date: 08.12.18
 * Time: 19:42
 */
class AppAsset extends BaseAsset
{

    public $css = [
        'css/common.css',
    ];

    public $js = [
        'js/common.js',
    ];

    public $depends = [
        JqueryAsset::class,
        BootstrapPluginAsset::class,
        MaskedInputAsset::class,
        LodashAsset::class,




//        LodashAsset::class,
//        BootstrapPluginAsset::class,
//        FancyAsset::class,
//        GrowlAsset::class,
//        VueAsset::class,
//        Select2Asset::class,
//        InputMaskAsset::class,
    ];

}