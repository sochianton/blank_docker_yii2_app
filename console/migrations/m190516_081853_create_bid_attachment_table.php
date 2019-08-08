<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bid_attachment}}`.
 */
class m190516_081853_create_bid_attachment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bid_attachment}}', [
            'bid_id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bid_attachment}}');
    }
}
