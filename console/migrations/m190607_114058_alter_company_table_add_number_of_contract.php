<?php

use yii\db\Migration;

/**
 * Class m190607_114058_alter_company_table_add_number_of_contract
 */
class m190607_114058_alter_company_table_add_number_of_contract extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%company}}', 'number_of_contract', $this->string()->null()->defaultValue(null)->after('address'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%company}}', 'number_of_contract');
    }
}
