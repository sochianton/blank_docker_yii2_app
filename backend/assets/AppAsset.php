<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
        'kartik\select2\Select2Asset',
        'kartik\daterange\DateRangePickerAsset',
        'yii\widgets\MaskedInputAsset',
        'kartik\widgets\WidgetAsset',
        'kartik\depdrop\DepDropAsset',
        'kartik\depdrop\DepDropExtAsset',
        'kartik\icons\FontAwesomeAsset',
        'kartik\export\ExportColumnAsset',
        'kartik\export\ExportMenuAsset',
        'kartik\file\FileInputAsset',
        'kartik\datetime\DateTimePickerAsset',
        'scl\activeform\ActiveFormAsset',

    ];
}
