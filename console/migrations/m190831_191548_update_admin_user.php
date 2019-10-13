<?php

use yii\db\Migration;

/**
 * Class m190831_191548_update_admin_user
 */
class m190831_191548_update_admin_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%admin}}', 'phone', $this->string(20)->null()->after('email'));
        $this->addColumn('{{%admin}}', 'photo', $this->string()->null()->after('status'));
        $this->addColumn('{{%admin}}', 'balance', $this->float()->unsigned()->defaultValue(0)->after('photo'));
        $this->addColumn('{{%admin}}', 'company_id', $this->integer()->unsigned()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190831_191548_update_admin_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190831_191548_update_admin_user cannot be reverted.\n";

        return false;
    }
    */
}
