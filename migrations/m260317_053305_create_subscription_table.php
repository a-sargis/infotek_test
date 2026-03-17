<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subscription}}`.
 */
class m260317_053305_create_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(20)->notNull(),
            'created_at' => $this->integer(),
        ]);

        $this->createIndex(
            'idx_subscription_phone_author',
            '{{%subscription}}',
            ['phone', 'author_id'],
            true
        );

        $this->addForeignKey(
            'fk-subscription-author_id',
            '{{%subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropForeignKey('fk-subscription-author_id', '{{%subscription}}');
        $this->dropIndex('idx_subscription_phone_author', '{{%subscription}}');
        $this->dropTable('{{%subscription}}');
    }
}
