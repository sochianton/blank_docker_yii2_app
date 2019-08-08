<?php

use yii\db\Migration;

/**
 * Class m190506_095824_add_auth_token_to_andmin
 */
class m190506_095824_add_auth_token_to_andmin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%admin}}', 'auth_key', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
