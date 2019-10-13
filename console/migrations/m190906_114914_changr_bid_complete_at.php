<?php

use yii\db\Migration;

/**
 * Class m190906_114914_changr_bid_complete_at
 */
class m190906_114914_changr_bid_complete_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropColumn('{{%bid}}', 'complete_at');
        $this->addColumn('{{%bid}}', 'complete_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190906_114914_changr_bid_complete_at cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190906_114914_changr_bid_complete_at cannot be reverted.\n";

        return false;
    }
    */
}
