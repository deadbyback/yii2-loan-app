<?php

use yii\db\Migration;

/**
 * Class m241227_184252_create_rbac_tables
 */
class m241227_224252_create_rbac_tables extends Migration
{
    public function safeUp()
    {
         $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ]);

        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
            'FOREIGN KEY ([[rule_name]]) REFERENCES {{%auth_rule}} ([[name]])' .
            ' ON DELETE SET NULL ON UPDATE CASCADE',
        ]);

        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
            'FOREIGN KEY ([[parent]]) REFERENCES {{%auth_item}} ([[name]])' .
            ' ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[child]]) REFERENCES {{%auth_item}} ([[name]])' .
            ' ON DELETE CASCADE ON UPDATE CASCADE',
        ]);

        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
            'FOREIGN KEY ([[item_name]]) REFERENCES {{%auth_item}} ([[name]])' .
            ' ON DELETE CASCADE ON UPDATE CASCADE',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');
    }
}
