<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task}}`.
 */
class m251128_104911_create_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task}}', [
            'id' => $this->primaryKey()->comment('Первичный ключ'),
            'title' => $this->string()->notNull()->comment('Название задачи'),
            'note' => $this->string()->comment('Подробности по задаче'),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Дата и время создания записи'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP')->comment('Дата и время последнего обновления записи'),
            'status' => $this->string()->notNull()->defaultValue('new')->comment('Статус'),
            'delete' => $this->boolean()->defaultValue(false)->comment('Удалено')
        ]);

        $this->execute("ALTER TABLE {{%task}} ADD CONSTRAINT task_unique UNIQUE KEY (title, created_at, `delete`)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%task}}');
    }
}
