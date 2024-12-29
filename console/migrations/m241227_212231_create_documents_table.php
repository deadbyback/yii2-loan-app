<?php

use yii\db\Migration;

/**
 * Class m241227_212231_create_documents_table
 */
class m241227_212231_create_documents_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%documents}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'type' => $this->string()->notNull(),
            'file_path' => $this->string()->notNull(),
            'original_name' => $this->string()->notNull(),
            'mime_type' => $this->string()->notNull(),
            'size' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-documents-user_id',
            '{{%documents}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-documents-user_id',
            '{{%documents}}'
        );
        $this->dropTable('{{%documents}}');
    }
}
