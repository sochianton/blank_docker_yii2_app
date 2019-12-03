<?php

use yii\db\Migration;

/**
 * Class m191201_110914_delete_user_balance_field
 */
class m191201_110914_delete_user_balance_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropColumn('{{%admin}}', 'balance');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191201_110914_delete_user_balance_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191201_110914_delete_user_balance_field cannot be reverted.\n";

        return false;
    }
    */
}
