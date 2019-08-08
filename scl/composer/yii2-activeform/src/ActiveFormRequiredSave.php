<?php

namespace scl\activeform;

use yii\widgets\ActiveForm;

class ActiveFormRequiredSave extends ActiveForm
{
    public function init()
    {
        $this->options = array_merge($this->options, ['data-require-confirm' => true]);
        parent::init();
        $this->registerAssets();
    }

    public function registerAssets()
    {
        $view = $this->getView();
        ActiveFormAsset::register($view);
    }
}
