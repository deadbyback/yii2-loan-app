<?php

use yii\db\Migration;

/**
 * Class m241227_212231_create_users_table
 */
class m241227_212230_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'password_hash' => $this->string()->notNull(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'date_of_birth' => $this->date()->notNull(),
            'passport_number' => $this->string()->notNull()->unique(),
            'passport_expiry_date' => $this->date()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex(
            'idx-users-email',
            '{{%users}}',
            'email'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-users-email',
            '{{%users}}'
        );
        $this->dropTable('{{%users}}');
    }
}
