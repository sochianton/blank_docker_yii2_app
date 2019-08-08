<?php

use yii\db\Migration;

/**
 * Class m190520_045429_alter_table_bid_attachment
 */
class m190520_045429_alter_table_bid_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%bid_attachment}}', 'original_name', $this->string()->after('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%bid_attachment}}', 'original_name');
    }
}
