<?php

use yii\db\Migration;

/**
 * Class m190524_074637_alter_bid_table
 */
class m190524_074637_alter_bid_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%bid}}', 'employee_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%bid}}', 'employee_id', $this->integer()->notNull());
    }
}
