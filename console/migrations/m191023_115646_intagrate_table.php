<?php

use yii\db\Migration;

/**
 * Class m191023_115646_intagrate_table
 */
class m191023_115646_intagrate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%euroservice_integrate}}', [
            'bid_id' => $this->integer()->unsigned()->notNull(),
            'rid' => $this->integer()->unsigned()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191023_115646_intagrate_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191023_115646_intagrate_table cannot be reverted.\n";

        return false;
    }
    */
}
