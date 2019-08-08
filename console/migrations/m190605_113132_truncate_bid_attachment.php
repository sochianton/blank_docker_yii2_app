<?php

use yii\db\Migration;

/**
 * Class m190605_113132_truncate_bid_attachment
 */
class m190605_113132_truncate_bid_attachment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->truncateTable('bid_attachment')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Nothing here
    }
}
