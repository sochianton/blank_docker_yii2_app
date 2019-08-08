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
        $this->dropTable('{{%qualification}}');
    }
}
