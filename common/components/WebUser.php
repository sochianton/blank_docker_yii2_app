<?php


namespace common\components;


use common\services\UserService;
use yii\web\User;

class WebUser extends User
{

    public function ch($permission, $params = [], $allowCaching = false){
        return $this->can($permission, $params, $allowCaching);
    }

    /**
     * @return \common\ar\User
     * @throws \yii\web\NotFoundHttpException
     */
    public function getModel(){
        return UserService::get($this->id);
    }

}