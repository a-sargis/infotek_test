<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%author}}`.
 */
class m260317_053203_create_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%author}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%author}}');
    }
}
