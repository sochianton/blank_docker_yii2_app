<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%employee_rejected_bid}}`.
 */
class m190516_092340_create_employee_rejected_bid_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%employee_rejected_bid}}', [
            'employee_id' => $this->integer()->unsigned()->notNull(),
            'bid_id' => $this->integer()->unsigned()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_rejected_bid}}');
    }
}
