<?php

use yii\db\Migration;

/**
 * Class m190829_190725_set_work_datetime
 */
class m190829_190725_set_work_datetime extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->dropColumn('{{%work}}', 'created_at');
        $this->dropColumn('{{%work}}', 'updated_at');
        $this->dropColumn('{{%work}}', 'deleted_at');

        $this->addColumn('{{%work}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%work}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%work}}', 'deleted_at', $this->timestamp()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_190725_set_work_datetime cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190829_190725_set_work_datetime cannot be reverted.\n";

        return false;
    }
    */
}
