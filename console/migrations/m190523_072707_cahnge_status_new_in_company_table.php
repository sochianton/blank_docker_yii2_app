<?php

use yii\db\Migration;

/**
 * Class m190523_072707_cahnge_status_new_in_company_table
 */
class m190523_072707_cahnge_status_new_in_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()
            ->update('{{%company}}', ['status' => 20], ['status' => 10])
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Nothing here
    }
}
