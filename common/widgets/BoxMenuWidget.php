<?php


namespace common\widgets;


use yii\widgets\Menu;

class BoxMenuWidget extends Menu
{

    public $options = [
        'tag' => null,
    ];

    public $itemOptions =  [
        'tag' => null,
    ];

    public $encodeLabels = false;
    public $linkTemplate = '<a class="btn btn-default" href="{url}">{label}</a>';

}