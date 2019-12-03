<?php

use yii\db\Migration;

/**
 * Class m190820_091719_add_price_in_work_table
 */
class m190820_091719_add_price_in_work_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('{{%work}}', 'price', $this->money()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190820_091719_add_price_in_work_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190820_091719_add_price_in_work_table cannot be reverted.\n";

        return false;
    }
    */
}
