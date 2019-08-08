<?php

use yii\db\Migration;

/**
 * Class m190515_032334_alter_bid_table_add_deleted_at
 */
class m190515_032334_alter_bid_table_add_deleted_at extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%bid}}', 'deleted_at', $this->integer()->null()->after('updated_at'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%bid}}', 'deleted_at');
    }
}
