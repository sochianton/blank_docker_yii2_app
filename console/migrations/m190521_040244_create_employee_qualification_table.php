<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%employee_qualification}}`.
 */
class m190521_040244_create_employee_qualification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%employee_qualification}}', [
            'employee_id' => $this->integer()->unsigned()->notNull(),
            'qualification_id' => $this->integer()->unsigned()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_qualification}}');
    }
}
