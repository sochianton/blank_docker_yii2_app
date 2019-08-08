<?php

use yii\db\Migration;

/**
 * Class m190605_065403_alter_work_table
 */
class m190605_065403_alter_work_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%work}}', 'category');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%work}}', 'category', $this->integer()->unsigned()->notNull());
    }
}
