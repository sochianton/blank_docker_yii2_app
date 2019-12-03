<?php


namespace common\filters;


use yii\filters\AccessControl;
use yii\helpers\Url;

class AdminRBACFilter extends AccessControl
{

    public function init()
    {



        $permission = Url::current(); //--    например /admin/users/admin?id=12
        $parts = explode('?', $permission);
        $permission = $parts[0];

        $base = \Yii::$app->request->baseUrl;
        $permission = substr($permission, strlen($base));

        $this->rules[] = [
            'allow' => true,
            'roles' => [$permission],

        ];


        parent::init();

        //\Yii::$app->_->di($this->rules);

    }

}