<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%work_qualification}}`.
 */
class m190507_114400_create_work_qualification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%work_qualification}}', [
            'work_id' => $this->integer()->unsigned()->notNull(),
            'qualification_id' => $this->integer()->unsigned()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%work_qualification}}');
    }
}
