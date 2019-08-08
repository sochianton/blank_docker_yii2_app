<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bid}}`.
 */
class m190513_034545_create_bid_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bid}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'customer_id' => $this->integer()->notNull(),
            'employee_id' => $this->integer()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'price' => $this->integer()->unsigned()->defaultValue(0),
            'object' => $this->string(),
            'complete_at' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%bid_work}}', [
            'bid_id' => $this->integer()->notNull(),
            'work_id' => $this->integer()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bid}}');
        $this->dropTable('{{%bid_work}}');
    }
}
