<?php

use yii\db\Migration;

/**
 * Class m191129_152924_add_create_update_by_field
 */
class m191129_152924_add_create_update_by_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->addColumn('{{%admin}}', 'created_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%admin}}', 'updated_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%admin}}', 'deleted_by', $this->integer()->null());

        $this->addColumn('{{%bid}}', 'created_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%bid}}', 'updated_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%bid}}', 'deleted_by', $this->integer()->null());

        $this->addColumn('{{%company}}', 'created_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%company}}', 'updated_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%company}}', 'deleted_by', $this->integer()->null());

        $this->addColumn('{{%qualification}}', 'created_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%qualification}}', 'updated_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%qualification}}', 'deleted_by', $this->integer()->null());

        $this->addColumn('{{%work}}', 'created_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%work}}', 'updated_by', $this->integer()->defaultValue(1)->notNull());
        $this->addColumn('{{%work}}', 'deleted_by', $this->integer()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191129_152924_add_create_update_by_field cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191129_152924_add_create_update_by_field cannot be reverted.\n";

        return false;
    }
    */
}
