<?php

use yii\db\Migration;

/**
 * Class m190829_195728_set_users_datetime
 */
class m190829_195728_set_users_datetime extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropColumn('{{%admin}}', 'created_at');
        $this->dropColumn('{{%admin}}', 'updated_at');
        //$this->dropColumn('{{%admin}}', 'deleted_at');

        $this->dropColumn('{{%customer}}', 'created_at');
        $this->dropColumn('{{%customer}}', 'updated_at');
        //$this->dropColumn('{{%customer}}', 'deleted_at');

        $this->dropColumn('{{%employee}}', 'created_at');
        $this->dropColumn('{{%employee}}', 'updated_at');
        //$this->dropColumn('{{%employee}}', 'deleted_at');


        $this->addColumn('{{%admin}}', 'deleted_at', $this->timestamp()->null());
        $this->addColumn('{{%admin}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%admin}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));

        $this->addColumn('{{%customer}}', 'deleted_at', $this->timestamp()->null());
        $this->addColumn('{{%customer}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%customer}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));

        $this->addColumn('{{%employee}}', 'deleted_at', $this->timestamp()->null());
        $this->addColumn('{{%employee}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%employee}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));





    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_195728_set_users_datetime cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190829_195728_set_users_datetime cannot be reverted.\n";

        return false;
    }
    */
}
