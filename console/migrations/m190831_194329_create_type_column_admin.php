<?php

use yii\db\Migration;

/**
 * Class m190831_194329_create_type_column_admin
 */
class m190831_194329_create_type_column_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%admin}}', 'type', $this->integer()->notNull()->defaultValue(0)->after('password_reset_token'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190831_194329_create_type_column_admin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190831_194329_create_type_column_admin cannot be reverted.\n";

        return false;
    }
    */
}
