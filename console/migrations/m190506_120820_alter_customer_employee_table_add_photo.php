<?php

use yii\db\Migration;

/**
 * Class m190506_120820_alter_customer_employee_table_add_photo
 */
class m190506_120820_alter_customer_employee_table_add_photo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%customer}}', 'photo', $this->string()->null()->after('status'));
        $this->addColumn('{{%employee}}', 'photo', $this->string()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%customer}}', 'photo');
        $this->dropColumn('{{%employee}}', 'photo');
    }
}
