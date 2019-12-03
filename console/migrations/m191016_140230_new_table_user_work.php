<?php

use yii\db\Migration;

/**
 * Class m191016_140230_new_table_user_work
 */
class m191016_140230_new_table_user_work extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('{{%user_work}}', [
            'work_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191016_140230_new_table_user_work cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191016_140230_new_table_user_work cannot be reverted.\n";

        return false;
    }
    */
}
