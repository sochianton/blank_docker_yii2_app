<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%qualification}}`.
 */
class m190507_034550_create_qualification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%qualification}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100)->notNull(),
            'deleted_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%qualification}}');
    }
}
