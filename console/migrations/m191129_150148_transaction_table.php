<?php

use yii\db\Migration;

/**
 * Class m191129_150148_transaction_table
 */
class m191129_150148_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%transactions}}', [
            'id' => $this->primaryKey(),
            'amount' => $this->money()->notNull(),
            'from' => $this->integer()->null(),
            'to' => $this->integer()->null(),
            'comment' => $this->string()->null(),



            'created_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('NOW()'),
            'deleted_at' => $this->timestamp()->null(),

            'created_by' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'deleted_by' => $this->integer()->null(),
        ], $tableOptions);

        $this->addForeignKey('user_transactions_from', '{{%transactions}}', 'from', '{{%admin}}', 'id');
        $this->addForeignKey('user_transactions_to', '{{%transactions}}', 'to', '{{%admin}}', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191129_150148_transaction_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191129_150148_transaction_table cannot be reverted.\n";

        return false;
    }
    */
}
