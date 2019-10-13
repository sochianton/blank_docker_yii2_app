<?php

use yii\db\Expression;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%admin}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'first_name' => $this->string()->notNull(),
            'second_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->null(),
        ], $tableOptions);

        $this->createTable('{{%customer}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'phone' => $this->string(20)->defaultValue(null),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'first_name' => $this->string()->notNull(),
            'second_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->null(),
        ], $tableOptions);

        $this->createTable('{{%employee}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'phone' => $this->string(20)->defaultValue(null),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'first_name' => $this->string()->notNull(),
            'second_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'created_at' => $this->timestamp()->null(),
            'updated_at' => $this->timestamp()->null(),
        ], $tableOptions);

        $this->createTable('{{%auth_token}}', [
            'token' => $this->string(40),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'expired_at' => $this->timestamp(0)->notNull(),
        ]);
        $this->addPrimaryKey('PK_tokens', '{{%auth_token}}', ['token']);

        $this->insert('admin', [
            'email' => 'admin@euroservice.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('123456'),
            'first_name' => 'Super',
            'second_name' => '',
            'last_name' => 'Admin',
            'status' => 10,
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);

        $this->insert('customer', [
            'email' => 'customer@euroservice.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('123456'),
            'first_name' => 'Custo',
            'second_name' => '',
            'last_name' => 'Mer',
            'status' => 10,
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);

        $this->insert('employee', [
            'email' => 'employee@euroservice.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('123456'),
            'first_name' => 'Emplo',
            'second_name' => '',
            'last_name' => 'Yee',
            'status' => 10,
            'created_at' => new Expression('NOW()'),
            'updated_at' => new Expression('NOW()'),
        ]);

        //$this->addForeignKey()
    }

    public function down()
    {
        $this->dropTable('{{%admin}}');
        $this->dropTable('{{%customer}}');
        $this->dropTable('{{%employee}}');
        $this->dropTable('{{%auth_token}}');
    }
}
