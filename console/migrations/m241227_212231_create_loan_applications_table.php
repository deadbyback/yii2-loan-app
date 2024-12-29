<?php

use yii\db\Migration;

/**
 * Class m241227_212231_create_loan_applications_table
 */
class m241227_212231_create_loan_applications_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%loan_applications}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(10, 2)->notNull(),
            'term_months' => $this->integer()->notNull(),
            'purpose' => $this->text()->notNull(),
            'monthly_income' => $this->decimal(10, 2)->notNull(),
            'status' => $this->string()->notNull()->defaultValue('pending'),
            'monthly_payment' => $this->decimal(10, 2),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-loan_applications-user_id',
            '{{%loan_applications}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-loan_applications-status',
            '{{%loan_applications}}',
            'status'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-loan_applications-status',
            '{{%loan_applications}}',
        );
        $this->dropForeignKey(
            'fk-loan_applications-user_id',
            '{{%loan_applications}}',
        );
        $this->dropTable('{{%loan_applications}}');
    }
}
