<?php

use yii\db\Migration;

/**
 * Class m190522_101645_alter_bid_table
 */
class m190522_101645_alter_bid_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%bid}}', 'customer_comment', $this->text()->null());
        $this->addColumn('{{%bid}}', 'employee_comment', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%bid}}', 'customer_comment');
        $this->dropColumn('{{%bid}}', 'employee_comment');
    }
}
