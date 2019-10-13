<?php

use yii\db\Migration;

/**
 * Class m190829_152832_set_qualification_datetime
 */
class m190829_152832_set_qualification_datetime extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%qualification}}', 'created_at');
        $this->dropColumn('{{%qualification}}', 'updated_at');
        $this->dropColumn('{{%qualification}}', 'deleted_at');

        $this->addColumn('{{%qualification}}', 'deleted_at', $this->timestamp()->null());
        $this->addColumn('{{%qualification}}', 'updated_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
        $this->addColumn('{{%qualification}}', 'created_at', $this->timestamp()->notNull()->defaultExpression('NOW()'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_152832_set_qualification_datetime cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190829_152832_set_qualification_datetime cannot be reverted.\n";

        return false;
    }
    */
}
