<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m190508_032807_create_company_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company}}', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(),
            'status' => $this->integer(),
            'name' => $this->string(100),
            'address' => $this->string(100),
            'deleted_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->defaultExpression('NOW()'),
        ]);

        $this->addColumn('{{%customer}}', 'company_id', $this->integer()->unsigned()->defaultValue(null));
        $this->addColumn('{{%employee}}', 'company_id', $this->integer()->unsigned()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company}}');

        $this->dropColumn('{{%customer}}', 'company_id');
        $this->dropColumn('{{%employee}}', 'company_id');
    }
}
