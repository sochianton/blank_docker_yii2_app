<?php


namespace common\components;


use yii\base\Component;


class CURL extends Component
{

    /**
     * Post запрос
     * @param $url
     * @param $data
     * @return mixed
     */
    static function post($url, $data){

        //Yii::log(json_encode(array($data), JSON_UNESCAPED_UNICODE), 'info', 'vsDesk');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Following line is compulsary to add as it is:
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        //curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //BODY
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $res = curl_exec($ch);

        curl_close($ch);
        return $res;

    }

    /**
     * Post запрос с авторизацией
     * @param $url
     * @param $data
     * @param $login
     * @param $password
     * @return mixed
     */
    static function authPost($url, $data, $login, $password){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Following line is compulsary to add as it is:
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //AUTH
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $login.':'.$password);

        //BODY
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;

    }

    /**
     * PUT запрос с авторизацией
     * @param $url
     * @param $data
     * @param $login
     * @param $password
     * @return mixed
     */
    static function authPut($url, $data, $login, $password){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        // Following line is compulsary to add as it is:
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POST, 1 );
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //AUTH
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $login.':'.$password);

        //BODY
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;

    }

    /**
     * Get запрос
     * @param $url
     * @param array $data
     * @return mixed
     */
    static function get($url, $data=array()){


        if(!empty($data)){
            $url = $url.'?'.http_build_query($data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;

    }

    /**
     * Get запрос
     * @param $url
     * @param array $data
     * @return mixed
     */
    static function authGet($url, $data=array(), $login, $password){


        if(!empty($data)){
            $url = $url.'?'.http_build_query($data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        //AUTH
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $login.':'.$password);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;

    }

}