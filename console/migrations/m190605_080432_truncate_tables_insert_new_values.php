<?php

use yii\db\Migration;

/**
 * Class m190605_080432_truncate_tables_insert_new_values
 */
class m190605_080432_truncate_tables_insert_new_values extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        Yii::$app->db->createCommand()->truncateTable('bid')->execute();
        Yii::$app->db->createCommand()->truncateTable('work')->execute();
        Yii::$app->db->createCommand()->truncateTable('qualification')->execute();
        Yii::$app->db->createCommand()->truncateTable('work_qualification')->execute();
        Yii::$app->db->createCommand()->truncateTable('employee_qualification')->execute();

        $time = time();

        $qualifications = [
            ['Электрика', $time, $time],
            ['Сантехника', $time, $time],
            ['Холодильное оборудование', $time, $time],
            ['Строительные работы', $time, $time]
        ];

        Yii::$app->db->createCommand()->batchInsert(
            'qualification',
            ['name', 'created_at', 'updated_at'],
            $qualifications
        )->execute();

        $works = [
            ['Установка', 1000, 0, $time, $time],
            ['Демонтаж', 1000, 0, $time, $time],
            ['Ремонт', 1000, 0, $time, $time],
            ['Диагностика', 1000, 0, $time, $time],
            ['Перемещение', 1000, 0, $time, $time],
            ['Инвентаризация', 1000, 0, $time, $time],
            ['Перебрендирование', 1000, 0, $time, $time],
            ['Ввод в эксплуатацию', 1000, 0, $time, $time],
            ['Разбор на запчасти', 1000, 0, $time, $time],
            ['Ремонт на складе', 1000, 0, $time, $time],
            ['Техническое обслуживание', 1000, 0, $time, $time],
            ['Перемещение со склада на склад', 1000, 0, $time, $time],
        ];

        Yii::$app->db->createCommand()->batchInsert(
            'work',
            ['name', 'price', 'commission', 'created_at', 'updated_at'],
            $works
        )->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Nothing here
    }
}
