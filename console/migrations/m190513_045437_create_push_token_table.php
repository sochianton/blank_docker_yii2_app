<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%push_token}}`.
 */
class m190513_045437_create_push_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%push_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned(),
            'user_type' => $this->integer()->unsigned(),
            'token' => $this->string()->unique(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%push_token}}');
    }
}
