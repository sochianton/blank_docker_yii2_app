<?php

use yii\db\Migration;

/**
 * Class m190507_054037_create_balance_tables
 */
class m190507_054037_create_balance_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%employee}}', 'balance', $this->integer()->unsigned()->defaultValue(0));

        $this->createTable('{{%employee_balance_history}}', [
            'id' => $this->primaryKey(),
            'amount' => $this->integer()->unsigned(),
            'user_id' => $this->integer(),
            'type' => $this->integer(),
            'created_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->null(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%employee}}', 'balance');
        $this->dropTable('{{%employee_balance_history}}');
    }
}
