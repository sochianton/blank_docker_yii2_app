<?php

namespace backend\assets;

use common\assets\AppAsset;
use common\assets\LteAsset;

/**
 * Main backend application asset bundle.
 */
class BackendAsset extends AppAsset
{

    public $sourcePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/backend.css',
    ];
    public $js = [
        'js/backend.js',
    ];
    public $depends = [

        AppAsset::class,
        LteAsset::class,

    ];
}
