<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%work}}`.
 */
class m190507_110229_create_work_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%work}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'price' => $this->integer()->unsigned()->defaultValue(0),
            'commission' => $this->integer(3)->unsigned()->defaultValue(0),
            'category' => $this->integer()->unsigned()->notNull(),
            'deleted_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%work}}');
    }
}
