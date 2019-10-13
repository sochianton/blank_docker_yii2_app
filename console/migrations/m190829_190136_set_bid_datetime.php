<?php

use yii\db\Migration;

/**
 * Class m190829_190136_set_bid_datetime
 */
class m190829_190136_set_bid_datetime extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropColumn('{{%bid}}', 'created_at');
        $this->dropColumn('{{%bid}}', 'updated_at');
        $this->dropColumn('{{%bid}}', 'deleted_at');

        $this->addColumn('{{%bid}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%bid}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%bid}}', 'deleted_at', $this->timestamp()->null());


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_190136_set_bid_datetime cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190829_190136_set_bid_datetime cannot be reverted.\n";

        return false;
    }
    */
}
